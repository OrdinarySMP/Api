<?php

namespace App\Repositories;

use App\Enums\ApplicationSubmissionState;
use App\Enums\DiscordButton;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApplicationSubmissionRepository
{
    public function sendApplicationSubmission(ApplicationSubmission $applicationSubmission): bool
    {
        $pingRoles = $applicationSubmission->application?->pingRoles->map(fn ($pingRole) => "<@&$pingRole->role_id>")->join(', ') ?? '';

        $response = Http::discordBot()
            ->post(
                '/channels/'.$applicationSubmission->application?->log_channel.'/messages',
                [
                    'content' => $pingRoles,
                    ...$this->getMessageData($applicationSubmission),
                ]
            );
        $data = $response->json();

        $applicationSubmission->message_id = $data['id'];
        $applicationSubmission->channel_id = $data['channel_id'];
        $applicationSubmission->saveQuietly();

        $this->createThread($applicationSubmission);

        $discordRepository = new DiscordRepository;
        $applicationSubmission->application?->pendingRoles
            ->each(function ($addRole) use ($applicationSubmission, $discordRepository) {
                $reason = "Application {$applicationSubmission->application?->name}";
                $discordRepository->addRoleToMember($addRole->role_id, $applicationSubmission->discord_id, $reason);
            });

        return $response->ok();
    }

    public function updateApplicationSubmission(ApplicationSubmission $applicationSubmission): bool
    {
        $action = match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => 'accepted',
            ApplicationSubmissionState::Denied => 'denied',
            default => 'handled',
        };
        $responseSent = $this->sendResponseToUser($applicationSubmission, $action);
        $roleResult = $this->handleRoles($applicationSubmission);

        $message = "<@{$applicationSubmission->handled_by}> {$action} <@{$applicationSubmission->discord_id}>\`s application for: `{$applicationSubmission->application?->name}`";

        if (! $responseSent) {
            $message .= "\n\nUnable to contact <@{$applicationSubmission->discord_id}>.";
        }

        if ($roleResult) {
            $message .= "\n\n{$roleResult}";
        }

        if ($applicationSubmission->applicationResponse) {
            $message .= "\n\nTemplate: `{$applicationSubmission->applicationResponse->name}`";
        } elseif ($applicationSubmission->custom_response) {
            $message .= "\n\nReason provided:\n```{$applicationSubmission->custom_response}```";
        }

        $response = Http::discordBot()
            ->patch(
                '/channels/'.$applicationSubmission->channel_id.'/messages/'.$applicationSubmission->message_id,
                [
                    'content' => $message,
                    ...$this->getMessageData($applicationSubmission),
                ]
            );

        return $response->ok();
    }

    /**
     * @return array{
     *         embeds: array<array{
     *             title: string,
     *             fields: array{array{
     *                 name: string,
     *                 value: string
     *             }},
     *             timestamp: string,
     *             color: string,
     *             thumbnail: array{url: string}
     *         }>,
     *         components: \Illuminate\Support\Collection<
     *             int,
     *             non-empty-array{
     *                 type?: int,
     *                 components?: array<array{
     *                     type: int,
     *                     custom_id: string,
     *                     style: DiscordButton,
     *                     label: string
     *                 }>
     *            }|non-empty-array{
     *                type?: int,
     *                components?: array<array{
     *                    type: int,
     *                    custom_id: string,
     *                    options: array<array{label:string, value:string, description:string}>,
     *                    placeholder: string,
     *                    min_values: int,
     *                    max_values: int,
     *                }>
     *            }>
     *        }
     */
    private function getMessageData(ApplicationSubmission $applicationSubmission): array
    {
        // add action row for template responses
        // 1 select per action row
        // 25 options per select
        // https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure

        $embed = $this->getSubmissionEmbed($applicationSubmission);
        $buttonActionRow = $this->getButtonsActionRow($applicationSubmission);
        $acceptActionRow = $this->getAcceptActionRow($applicationSubmission);
        $denyActionRow = $this->getDenyActionRow($applicationSubmission);
        $components = collect([$buttonActionRow, $acceptActionRow, $denyActionRow])->filter(fn ($component) => ! empty($component));

        return [
            'embeds' => [$embed],
            'components' => $components,
        ];
    }

    /**
     * @return array{
     *         title: string,
     *         fields: array{array{
     *             name: string,
     *             value: string
     *         }},
     *         timestamp: string,
     *         color: string,
     *         thumbnail: array{url: string}
     *        }
     */
    private function getSubmissionEmbed(ApplicationSubmission $applicationSubmission): array
    {
        $member = $this->getMember($applicationSubmission);
        $statsField = $this->getStatsField($applicationSubmission, $member);
        $tooLongField = $this->getTooLongField();
        $applicationQuestionAnswers = $applicationSubmission->applicationQuestionAnswers;
        $title = "{$member['user']['global_name']}`s application for {$applicationSubmission->application?->name}";

        if ($applicationQuestionAnswers->count() > 25) {
            return [
                'title' => $title,
                'fields' => [$tooLongField, $statsField],
                'timestamp' => now()->toIso8601String(),
                'color' => $this->getEmbedColor($applicationSubmission),
                'thumbnail' => [
                    'url' => "https://cdn.discordapp.com/avatars/{$member['user']['id']}/{$member['user']['avatar']}.png",
                ],
            ];
        }

        $totalLenght = 0;
        $fields = [];

        $applicationSubmission->applicationQuestionAnswers->each(function ($applicationQuestionAnswer) use (&$fields, &$totalLenght) {
            $answer = strlen($applicationQuestionAnswer->answer) < 1024 ?
                $applicationQuestionAnswer->answer :
                'This answer is too long. Please view in the helper panel.';
            $question = $applicationQuestionAnswer->applicationQuestion->question ?? '';

            $totalLenght += strlen($answer);
            $totalLenght += strlen($question);

            $fields[] = [
                'name' => $question,
                'value' => $answer,
            ];
        });

        $totalLenght += strlen($statsField['name']);
        $totalLenght += strlen($statsField['value']);

        $fields[] = $statsField;

        $totalLenght += strlen($title);

        if ($totalLenght > 6000) {
            return [
                'title' => $title,
                'fields' => [$tooLongField, $statsField],
                'timestamp' => now()->toIso8601String(),
                'color' => $this->getEmbedColor($applicationSubmission),
                'thumbnail' => [
                    'url' => "https://cdn.discordapp.com/avatars/{$member['user']['id']}/{$member['user']['avatar']}.png",
                ],
            ];
        }

        return [
            'title' => $title,
            'fields' => $fields,
            'timestamp' => now()->toIso8601String(),
            'color' => $this->getEmbedColor($applicationSubmission),
            'thumbnail' => [
                'url' => "https://cdn.discordapp.com/avatars/{$member['user']['id']}/{$member['user']['avatar']}.png",
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getMember(ApplicationSubmission $applicationSubmission): array
    {
        $discordRepository = new DiscordRepository;

        return $discordRepository->getGuildMemberById($applicationSubmission->discord_id);
    }

    public function getEmbedColor(ApplicationSubmission $applicationSubmission): string
    {
        return (string) match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => hexdec('0dff00'),
            ApplicationSubmissionState::Denied => hexdec('ff0019'),
            default => hexdec('ffcc00'),
        };
    }

    /**
     * @param  array<mixed>  $member
     * @return array{name:string, value:string}
     */
    private function getStatsField(ApplicationSubmission $applicationSubmission, array $member): array
    {
        $applicationSubmissionCount = ApplicationSubmission::where('discord_id', $applicationSubmission->discord_id)
            ->where('application_id', $applicationSubmission->application_id)
            ->count();
        $joinedAt = Carbon::parse($member['joined_at'])->timestamp;
        $duration = now()->diffForHumans($applicationSubmission->created_at, \Carbon\CarbonInterface::DIFF_ABSOLUTE);
        $stats =
            "**User ID:** {$applicationSubmission->discord_id}\n".
            "**Username:** {$member['user']['username']}\n".
            "**User Mention:** <@{$applicationSubmission->discord_id}>\n".
            "**Application Duration:** {$duration}\n".
            "**Time on Server:** <t:{$joinedAt}:R>\n".
            "**Application Number:** {$applicationSubmissionCount}";
        $statsTitle = 'Application Stats:';

        return [
            'name' => $statsTitle,
            'value' => $stats,
        ];
    }

    /**
     * @return array{name:string, value:string}
     */
    private function getTooLongField(): array
    {
        return [
            'name' => '**Application too long**',
            'value' => 'Please view the application on the helper panel',
        ];
    }

    /**
     * @return array{
     *     type?: int,
     *     components?: array<array{
     *         type: int,
     *         custom_id: string,
     *         style: DiscordButton,
     *         label: string
     *     }>
     * }
     */
    private function getButtonsActionRow(ApplicationSubmission $applicationSubmission): array
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending) {
            return [];
        }
        $buttons = collect();
        $buttons->push([
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-accept-'.$applicationSubmission->id,
            'style' => DiscordButton::Success,
            'label' => 'Accept',
        ]);
        $buttons->push([
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-deny-'.$applicationSubmission->id,
            'style' => DiscordButton::Danger,
            'label' => 'Deny',
        ]);
        $buttons->push([
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-acceptWithReason-'.$applicationSubmission->id,
            'style' => DiscordButton::Success,
            'label' => 'Accept with reason',
        ]);
        $buttons->push([
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-denyWithReason-'.$applicationSubmission->id,
            'style' => DiscordButton::Danger,
            'label' => 'Deny with reason',
        ]);

        return [
            'type' => 1,
            'components' => $buttons->toArray(),
        ];
    }

    private function sendResponseToUser(ApplicationSubmission $applicationSubmission, string $action): bool
    {
        if ($applicationSubmission->applicationResponse) {
            $message = $applicationSubmission->applicationResponse->response;
        } elseif ($applicationSubmission->custom_response) {
            $message = $applicationSubmission->custom_response;
        } else {
            $message = match ($applicationSubmission->state) {
                ApplicationSubmissionState::Accepted => $applicationSubmission->application?->accept_message,
                ApplicationSubmissionState::Denied => $applicationSubmission->application?->deny_message,
                default => null,
            };
        }

        if (! $message) {
            return false;
        }

        $channelResponse = Http::discordBot()->post('/users/@me/channels', ['recipient_id' => $applicationSubmission->discord_id]);
        if ($channelResponse->failed()) {
            return false;
        }
        $channel = $channelResponse->json();
        $channelResponse = Http::discordBot()->post('/channels/'.$channel['id'].'/messages', [
            'embeds' => [
                [
                    'title' => "Your application for `{$applicationSubmission->application?->name}` has been {$action}",
                    'description' => $message,
                    'color' => $this->getEmbedColor($applicationSubmission),
                ],
            ],
        ]);

        return $channelResponse->ok();
    }

    private function handleRoles(ApplicationSubmission $applicationSubmission): string
    {
        $discordRepository = new DiscordRepository;

        $addRoles = match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => $applicationSubmission->application?->acceptedRoles,
            ApplicationSubmissionState::Denied => $applicationSubmission->application?->deniedRoles,
            default => null,
        } ?? collect();
        $removeRoles = match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => $applicationSubmission->application?->acceptRemovalRoles,
            ApplicationSubmissionState::Denied => $applicationSubmission->application?->denyRemovalRoles,
            default => null,
        } ?? collect();

        $removeRoles = $removeRoles->merge($applicationSubmission->application->pendingRoles ?? collect());

        $addResult = $addRoles->map(function ($addRole) use ($applicationSubmission, $discordRepository) {
            $reason = "Application {$applicationSubmission->application?->name}";
            $result = $discordRepository->addRoleToMember($addRole->role_id, $applicationSubmission->discord_id, $reason);
            if (! $result) {
                return "Could not add role <@&{$addRole->role_id}>.";
            }

            return '';
        })->collect();

        $removeResult = $removeRoles->map(function ($addRole) use ($applicationSubmission, $discordRepository) {
            $reason = "Application {$applicationSubmission->application?->name}";
            $result = $discordRepository->removeRoleFromMember($addRole->role_id, $applicationSubmission->discord_id, $reason);
            if (! $result) {
                return "Could not remove role <@&{$addRole->role_id}>.";
            }

            return '';
        })->collect();

        return $addResult->merge($removeResult)
            ->filter(fn ($result) => $result !== '')
            ->join("\n");
    }

    private function createThread(ApplicationSubmission $applicationSubmission): void
    {
        $member = $this->getMember($applicationSubmission);
        $threadResponse = Http::discordBot()
            ->post(
                "/channels/{$applicationSubmission->channel_id}/messages/{$applicationSubmission->message_id}/threads",
                [
                    'name' => "{$applicationSubmission->application?->name} - {$member['user']['username']}",
                ]
            );
        $thread = $threadResponse->json();
        $applicationSubmission->applicationQuestionAnswers
            ->each(function ($applicationQuestionAnswer) use ($thread) {
                if (! $applicationQuestionAnswer->attachments) {
                    return;
                }
                Http::discordBot()
                    ->post(
                        '/channels/'.$thread['id'].'/messages',
                        [
                            'content' => "**{$applicationQuestionAnswer->applicationQuestion?->question}**\n{$applicationQuestionAnswer->attachments}",
                        ]
                    );
            });
    }

    /**
     * @return array{
     *     type?: int,
     *     components?: array<array{
     *         type: int,
     *         custom_id: string,
     *         options: array<array{label:string, value:string, description:string}>,
     *         placeholder: string,
     *         min_values: int,
     *         max_values: int,
     *     }>
     * }
     */
    private function getAcceptActionRow(ApplicationSubmission $applicationSubmission): array
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending) {
            return [];
        }
        $options = $applicationSubmission->application?->acceptedResponses()->limit(25)->get()->map(fn ($response) => [
            'label' => $response->name,
            'value' => "{$response->id}",
            'description' => Str::limit($response->response, 90),
        ]) ?? collect();

        if ($options->isEmpty()) {
            return [];
        }

        return [
            'type' => 1,
            'components' => [[
                'type' => 3,
                'custom_id' => "applicationSubmission-acceptTemplate-{$applicationSubmission->id}",
                'options' => $options->toArray(),
                'placeholder' => 'Accept template',
                'min_values' => 1,
                'max_values' => 1,
            ]],
        ];
    }

    /**
     * @return array{
     *     type?: int,
     *     components?: array<array{
     *         type: int,
     *         custom_id: string,
     *         options: array<array{label:string, value:string, description:string}>,
     *         placeholder: string,
     *         min_values: int,
     *         max_values: int,
     *     }>
     * }
     */
    private function getDenyActionRow(ApplicationSubmission $applicationSubmission): array
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending) {
            return [];
        }
        $options = $applicationSubmission->application?->deniedResponses()->limit(25)->get()->map(fn ($response) => [
            'label' => $response->name,
            'value' => "{$response->id}",
            'description' => Str::limit($response->response, 90),
        ]) ?? collect();

        if ($options->isEmpty()) {
            return [];
        }

        return [
            'type' => 1,
            'components' => [[
                'type' => 3,
                'custom_id' => "applicationSubmission-denyTemplate-{$applicationSubmission->id}",
                'options' => $options->toArray(),
                'placeholder' => 'Deny templates',
                'min_values' => 1,
                'max_values' => 1,
            ]],
        ];
    }
}
