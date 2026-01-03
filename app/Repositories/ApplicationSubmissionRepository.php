<?php

namespace App\Repositories;

use App\Data\Discord\Component\ActionRowData;
use App\Data\Discord\Component\ButtonData;
use App\Data\Discord\Component\StringCollectorData;
use App\Data\Discord\Component\StringCollectorOptionData;
use App\Data\Discord\Embed\EmbedData;
use App\Data\Discord\Embed\FieldsData;
use App\Data\Discord\Embed\ThumbnailData;
use App\Data\Discord\MemberData;
use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApplicationSubmissionRepository
{
    public function sendApplicationSubmission(ApplicationSubmission $applicationSubmission): bool
    {
        $pingRoles = $applicationSubmission->application?->pingRoles->map(fn ($pingRole) => "<@&$pingRole->role_id>")->join(', ') ?? '';
        $message = "<@{$applicationSubmission->discord_id}>\`s application for: `{$applicationSubmission->application?->name}`";

        $response = Http::discordBot()
            ->post(
                '/channels/'.$applicationSubmission->application?->log_channel.'/messages',
                [
                    'content' => "{$pingRoles}\n{$message}",
                    ...$this->getMessageData($applicationSubmission),
                ]
            );
        $data = $response->json();
        if (! $response->ok()) {
            Log::error('Could not send submission:', $data);

            return false;
        }

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
        $responseSent = $this->sendResponseToUser($applicationSubmission);
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
        if (! $response->ok()) {
            Log::error('Could not update submission:', $response->json());
        }

        return $response->ok();
    }

    /**
     * @return array{
     *         embeds: Collection<int, EmbedData>,
     *         components: Collection<int, ActionRowData>
     *        }
     */
    private function getMessageData(ApplicationSubmission $applicationSubmission): array
    {
        $embed = $this->getSubmissionEmbed($applicationSubmission);
        $buttonActionRow = $this->getButtonsActionRow($applicationSubmission);
        $acceptActionRow = $this->getAcceptActionRow($applicationSubmission);
        $denyActionRow = $this->getDenyActionRow($applicationSubmission);
        $components = collect([$buttonActionRow, $acceptActionRow, $denyActionRow])->filter(fn ($component) => $component !== null)->values();

        return [
            'embeds' => collect([$embed]),
            'components' => $components,
        ];
    }

    private function getSubmissionEmbed(ApplicationSubmission $applicationSubmission): EmbedData
    {
        $member = $this->getMember($applicationSubmission);
        $statsField = $this->getStatsField($applicationSubmission, $member);
        $tooLongField = $this->getTooLongField();
        $applicationQuestionAnswers = $applicationSubmission->applicationQuestionAnswers;
        $name = $member->user->global_name ?? $member->user?->username;

        $applicationSubmissionCount = ApplicationSubmission::completed()
            ->where('discord_id', $applicationSubmission->discord_id)
            ->where('application_id', $applicationSubmission->application_id)
            ->count();

        $title = "{$name}`s application for {$applicationSubmission->application?->name}";

        if ($member->user?->avatar) {
            $avatar = "https://cdn.discordapp.com/avatars/{$member->user->id}/{$member->user->avatar}.png";
        } else {
            $index = ((int) $member->user?->id >> 22) % 6;
            $avatar = "https://cdn.discordapp.com/embed/avatars/{$index}.png";
        }
        $thumbnail = new ThumbnailData($avatar);

        if ($applicationQuestionAnswers->count() > 25) {
            return new EmbedData(
                title: $title,
                description: "Application Number: **{$applicationSubmissionCount}**",
                color: $this->getEmbedColor($applicationSubmission),
                fields: collect([$tooLongField, $statsField]),
                timestamp: now()->toIso8601String(),
                thumbnail: $thumbnail,
            );
        }

        $totalLenght = 0;
        $fields = collect([]);

        $applicationSubmission->applicationQuestionAnswers->each(function ($applicationQuestionAnswer) use (&$fields, &$totalLenght) {
            $answer = strlen($applicationQuestionAnswer->answer) < 1024 ?
                $applicationQuestionAnswer->answer :
                'This answer is too long. Please view in the thread.';
            $question = $applicationQuestionAnswer->applicationQuestion->question ?? '';

            $totalLenght += strlen($answer);
            $totalLenght += strlen($question);

            $fields->push(new FieldsData(
                name: "**{$applicationQuestionAnswer->applicationQuestion?->order}. {$question}**",
                value: $answer,
            ));
        });

        $totalLenght += strlen($statsField->name);
        $totalLenght += strlen($statsField->value);

        $fields->push($statsField);

        $totalLenght += strlen($title);

        if ($totalLenght > 6000) {
            return new EmbedData(
                title: $title,
                description: "Application Number: **{$applicationSubmissionCount}**",
                color: $this->getEmbedColor($applicationSubmission),
                fields: collect([$tooLongField, $statsField]),
                timestamp: now()->toIso8601String(),
                thumbnail: $thumbnail,
            );
        }

        return new EmbedData(
            title: $title,
            description: "Application Number: **{$applicationSubmissionCount}**",
            color: $this->getEmbedColor($applicationSubmission),
            fields: $fields,
            timestamp: now()->toIso8601String(),
            thumbnail: $thumbnail,
        );
    }

    public function getMember(ApplicationSubmission $applicationSubmission): MemberData
    {
        $discordRepository = new DiscordRepository;

        $member = $discordRepository->getGuildMemberById($applicationSubmission->discord_id);
        if (! $member) {
            Log::error("Could not find member for user id: {$applicationSubmission->discord_id}.");
            throw new \Error("Could not find member for user id: {$applicationSubmission->discord_id}.");
        }

        return $member;
    }

    public function getEmbedColor(ApplicationSubmission $applicationSubmission): string
    {
        return (string) match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => hexdec('0dff00'),
            ApplicationSubmissionState::Denied => hexdec('ff0019'),
            default => hexdec('ffcc00'),
        };
    }

    private function getStatsField(ApplicationSubmission $applicationSubmission, MemberData $member): FieldsData
    {
        $applicationSubmissionCount = ApplicationSubmission::completed()
            ->where('discord_id', $applicationSubmission->discord_id)
            ->where('application_id', $applicationSubmission->application_id)
            ->count();
        $joinedAt = Carbon::parse($member->joined_at)->timestamp;

        $submittedAt = ($applicationSubmission->submitted_at ?? now());
        $duration = $submittedAt->diffForHumans($applicationSubmission->created_at, \Carbon\CarbonInterface::DIFF_ABSOLUTE);
        $tag = $member->user?->primary_guild?->tag ? $member->user->primary_guild->tag : '---';
        $stats =
            "**User ID:** {$applicationSubmission->discord_id}\n".
            "**Username:** {$member->user?->username}\n".
            "**User Mention:** <@{$applicationSubmission->discord_id}>\n".
            "**Tag:** {$tag}\n".
            "**Application Duration:** {$duration}\n".
            "**Time on Server:** <t:{$joinedAt}:R>\n".
            "**Application Number:** {$applicationSubmissionCount}";
        $statsTitle = 'Application Stats:';

        return new FieldsData(
            name: $statsTitle,
            value: $stats,
        );
    }

    private function getTooLongField(): FieldsData
    {
        return new FieldsData(
            name: '**Application too long**',
            value: 'Please view the application on the thread',
        );
    }

    private function getButtonsActionRow(ApplicationSubmission $applicationSubmission): ActionRowData
    {
        $buttons = collect();
        if ($applicationSubmission->state === ApplicationSubmissionState::Pending) {

            $buttons->push(ButtonData::success(
                'applicationSubmission-accept-'.$applicationSubmission->id,
                'Accept',
            ));
            $buttons->push(ButtonData::danger(
                'applicationSubmission-deny-'.$applicationSubmission->id,
                'Deny',
            ));
            $buttons->push(ButtonData::success(
                'applicationSubmission-acceptWithReason-'.$applicationSubmission->id,
                'Accept with reason',
            ));
            $buttons->push(ButtonData::danger(
                'applicationSubmission-denyWithReason-'.$applicationSubmission->id,
                'Deny with reason',
            ));
        }

        $buttons->push(ButtonData::primary(
            'applicationSubmission-history-'.$applicationSubmission->id,
            'History',
        ));

        return new ActionRowData(components: $buttons);
    }

    private function sendResponseToUser(ApplicationSubmission $applicationSubmission): bool
    {
        $action = match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => 'accepted',
            default => 'reviewed',
        };

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
        if (! $channelResponse->ok()) {
            Log::error('Could not send response to user:', $channelResponse->json());
        }

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
                    'name' => "{$applicationSubmission->application?->name} - {$member->user?->username}",
                ]
            );
        $thread = $threadResponse->json();
        if (! $threadResponse->created()) {
            Log::error('Could not create thread:', $thread);

            return;
        }
        $applicationSubmission->applicationQuestionAnswers
            ->each(function ($applicationQuestionAnswer) use ($thread) {
                if (strlen($applicationQuestionAnswer->answer) > 1024) {
                    $limit = 2000 - strlen("**{$applicationQuestionAnswer->applicationQuestion?->question}**\n");
                    foreach ($this->chunkTextBySpace($limit, $applicationQuestionAnswer->answer) as $chunk) {
                        Http::discordBot()
                            ->post(
                                '/channels/'.$thread['id'].'/messages',
                                [
                                    'content' => "**{$applicationQuestionAnswer->applicationQuestion?->question}**\n{$chunk}",
                                ]
                            );
                    }
                } elseif ($applicationQuestionAnswer->attachments) {
                    $limit = 2000 - strlen("**{$applicationQuestionAnswer->applicationQuestion?->question}**\n");
                    foreach ($this->chunkTextBySpace($limit, $applicationQuestionAnswer->attachments) as $chunk) {
                        Http::discordBot()
                            ->post(
                                '/channels/'.$thread['id'].'/messages',
                                [
                                    'content' => "**{$applicationQuestionAnswer->applicationQuestion?->question}**\n{$chunk}",
                                ]
                            );
                    }
                }
            });
    }

    /**
     * @return string[]
     */
    private function chunkTextBySpace(int $limit, string $text): array
    {
        $answer = explode(' ', $text);
        $current = '';
        $chunks = [];
        foreach ($answer as $answerPart) {
            $nextCurrent = trim(implode(' ', [$current, $answerPart]));
            if (strlen($nextCurrent) > $limit) {
                $chunks[] = $current;
                $current = '';
            }
            $current = trim(implode(' ', [$current, $answerPart]));
        }
        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function getAcceptActionRow(ApplicationSubmission $applicationSubmission): ?ActionRowData
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending ||
            ! $applicationSubmission->application) {
            return null;
        }

        /** @var Collection<int, StringCollectorOptionData> $options */
        $options = StringCollectorOptionData::collect($applicationSubmission->application->acceptedResponses()->limit(25)->get());

        if ($options->isEmpty()) {
            return null;
        }
        $row = new StringCollectorData(
            custom_id: "applicationSubmission-acceptTemplate-{$applicationSubmission->id}",
            options: $options,
            placeholder: 'Accept template',
            min_values: 1,
            max_values: 1,
        );

        return new ActionRowData(components: collect([$row]));
    }

    private function getDenyActionRow(ApplicationSubmission $applicationSubmission): ?ActionRowData
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending ||
            ! $applicationSubmission->application) {
            return null;
        }

        /** @var Collection<int, StringCollectorOptionData> $options */
        $options = StringCollectorOptionData::collect($applicationSubmission->application->deniedResponses()->limit(25)->get());

        if ($options->isEmpty()) {
            return null;
        }
        $row = new StringCollectorData(
            custom_id: "applicationSubmission-denyTemplate-{$applicationSubmission->id}",
            options: $options,
            placeholder: 'Deny template',
            min_values: 1,
            max_values: 1,
        );

        return new ActionRowData(components: collect([$row]));
    }
}
