<?php

namespace App\Repositories;

use App\Enums\ApplicationSubmissionState;
use App\Enums\DiscordButton;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ApplicationSubmissionRepository
{
    public function sendApplicationSubmission(ApplicationSubmission $applicationSubmission): bool
    {
        $member = $this->getMember($applicationSubmission);

        $response = Http::discordBot()
            ->post(
                '/channels/'.$applicationSubmission->application->log_channel.'/messages',
                $this->getMessageData($applicationSubmission)
            )->throw();
        $data = $response->json();

        $applicationSubmission->message_id = $data['id'];
        $applicationSubmission->channel_id = $data['channel_id'];
        $applicationSubmission->saveQuietly();

        $response = Http::discordBot()
            ->post(
                "/channels/{$applicationSubmission->channel_id}/messages/{$applicationSubmission->message_id}/threads",
                [
                    'name' => "{$applicationSubmission->application->name} - {$member['user']['username']}",
                ]
            );

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
        $message = "<@{$applicationSubmission->handled_by}> {$action} <@{$applicationSubmission->discord_id}>\`s application for: `{$applicationSubmission->application->name}`";
        if (! $responseSent) {
            $message += "\n\n Unable to contact <@{$applicationSubmission->discord_id}>.";
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
     * @return array{embeds:array<mixed>,componentes:array<mixed>}
     */
    private function getMessageData(ApplicationSubmission $applicationSubmission): array
    {
        // add action row for template responses
        // 1 select per action row
        // 25 options per select
        // https://discord.com/developers/docs/interactions/message-components#select-menu-object-select-menu-structure

        $embed = $this->getSubmissionEmbed($applicationSubmission);
        $buttonActionRow = $this->getButtonsActionRow($applicationSubmission);
        $components = collect([$buttonActionRow])->filter(fn ($component) => ! empty($component));

        return [
            'embeds' => [$embed],
            'components' => $components,
        ];
    }

    /**
     * @return array<array{name:string, value:string}>
     */
    private function getSubmissionEmbed(ApplicationSubmission $applicationSubmission): array
    {
        $member = $this->getMember($applicationSubmission);
        $statsField = $this->getStatsField($applicationSubmission, $member);
        $tooLongField = $this->getTooLongField();
        $applicationQuestionAnswers = $applicationSubmission->applicationQuestionAnswers;
        if ($applicationQuestionAnswers->count() > 25) {
            return [$tooLongField, $statsField];
        }

        $totalLenght = 0;
        $fields = [];

        $applicationSubmission->applicationQuestionAnswers->each(function ($applicationQuestionAnswer) use (&$fields, &$totalLenght) {
            $answer = strlen($applicationQuestionAnswer->answer) < 1024 ?
                $applicationQuestionAnswer->answer :
                'This answer is too long. Please view in the helper panel.';
            $question = $applicationQuestionAnswer->applicationQuestion->question;

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

        $title = "{$member['user']['global_name']}`s application for {$applicationSubmission->application->name}";
        $totalLenght += strlen($title);

        if ($totalLenght > 6000) {
            return [$tooLongField, $statsField];
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
        return match ($applicationSubmission->state) {
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
        $duration = $applicationSubmission->created_at->diffForHumans(now(), ['parts' => 2]);
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
     *     type: int,
     *     components: array<array{
     *         type: int,
     *         custom_id: string,
     *         style: int,
     *         label: string
     *     }>
     * }
     */
    private function getButtonsActionRow(ApplicationSubmission $applicationSubmission): array
    {
        if ($applicationSubmission->state !== ApplicationSubmissionState::Pending) {
            return [];
        }
        $acceptButton = [
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-accept-'.$applicationSubmission->id,
            'style' => DiscordButton::Success,
            'label' => 'Accept',
        ];
        $closeButton = [
            'type' => 2, // button
            'custom_id' => 'applicationSubmission-deny-'.$applicationSubmission->id,
            'style' => DiscordButton::Danger,
            'label' => 'Deny',
        ];

        return [
            'type' => 1,
            'components' => [$acceptButton, $closeButton],
        ];
    }

    private function sendResponseToUser(ApplicationSubmission $applicationSubmission, string $action): bool
    {
        $message = match ($applicationSubmission->state) {
            ApplicationSubmissionState::Accepted => $applicationSubmission->application->accept_message,
            ApplicationSubmissionState::Denied => $applicationSubmission->application->deny_message,
            default => null,
        };
        if (! $message) {
            return false;
        }

        $channelResponse = Http::discordBot()->post('/users/@me/channels', ['recipient_id' => $applicationSubmission->discord_id]);
        if ($channelResponse->failed()) {
            return false;
        }
        $channel = $channelResponse->json();
        $channelResponse = Http::discordBot()->post('/channels/'.$channel['id'].'/messages', [
            'content' => $message,
            'embeds' => [
                [
                    'title' => "Your application for `{$applicationSubmission->application->name}` has been {$action}",
                    'description' => $message,
                    'color' => $this->getEmbedColor($applicationSubmission),
                ],
            ],
        ]);

        return $channelResponse->ok();
    }
}
