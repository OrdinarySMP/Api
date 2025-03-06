<?php

namespace App\Repositories;

use App\Enums\DiscordButton;
use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\TicketButton;
use App\Models\TicketConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TicketRepository
{
    public function __construct(
        protected DiscordRepository $discordRepository,
    ) {}

    public function getChannelName(Ticket $ticket): string
    {
        if (! $ticket->ticketButton) {
            return 'no channel';
        }

        return Str::replace('%id%', "$ticket->id", $ticket->ticketButton->naming_scheme);
    }

    /**
     * @param  array{ticket_button_id:string,created_by_discord_user_id:string}  $data
     */
    public function createForButton(TicketButton $ticketButton, array $data): Ticket
    {
        $roles = $this->discordRepository->roles();
        /**
         * @var array{id:string} $everyoneRole
         */
        $everyoneRole = $roles->firstWhere('name', '@everyone');

        $ticket = Ticket::create([
            ...$data,
            'state' => TicketState::Open,
        ]);

        $channelName = $this->getChannelName($ticket);
        $guildId = config('services.discord.server_id');

        /**
         * @var TicketConfig $ticketConfig
         */
        $ticketConfig = TicketConfig::where('guild_id', $guildId)->first();

        $teamOverrides = $ticketButton->ticketTeam?->ticketTeamRoles->map(function ($ticketTeamRole) {
            return [
                'id' => $ticketTeamRole->role_id,
                'type' => 0, // role
                'allow' => 1 << 10, // view channel permission
            ];
        }) ?? collect();

        // create channel
        $response = Http::discordBot()->post('/guilds/'.$guildId.'/channels', [
            'name' => $channelName,
            'topic' => $ticketButton->text,
            'parent_id' => $ticketConfig->category_id,
            'permission_overwrites' => [
                [
                    'id' => $everyoneRole['id'], // everyone
                    'type' => 0, // role
                    'deny' => 1 << 10, // view channel permission
                ],
                [
                    'id' => $data['created_by_discord_user_id'],
                    'type' => 1, // user
                    'allow' => 1 << 10, // view channel permission
                ],
                ...$teamOverrides,
            ],
        ]);

        if (! $response->created()) {
            throw new \Exception('Could not create channel');
        }
        $channelData = $response->json();

        $ticket->update([
            'channel_id' => $channelData['id'],
        ]);

        return $ticket;
    }

    public function pingRoles(Ticket $ticket): bool
    {
        $pingRoles = $ticket->ticketButton?->ticketButtonPingRoles->map(function ($ticketButtonPingRole) {
            return '<@&'.$ticketButtonPingRole->role_id.'>';
        }) ?? collect();

        if ($pingRoles->isEmpty()) {
            return true;
        }

        $pingResponse = Http::discordBot()->post('/channels/'.$ticket->channel_id.'/messages', [
            'content' => $pingRoles->join(', '),
        ]);

        $deleteResponse = null;
        if ($pingResponse->ok()) {
            $message = $pingResponse->json();
            $deleteResponse = Http::discordBot()->delete('/channels/'.$ticket->channel_id.'/messages/'.$message['id']);
        }

        return $pingResponse->ok() && $deleteResponse?->ok();
    }

    /**
     * @return array<mixed>
     */
    public function sendInitialMessage(Ticket $ticket): array
    {
        $closeButton = [
            'type' => 2, // button
            'custom_id' => 'ticket-close-'.$ticket->id,
            'style' => DiscordButton::Danger,
            'label' => 'Close',
            'emoji' => ['name' => '🔒'],
        ];

        $closeWithReasonButton = [
            'type' => 2, // button
            'custom_id' => 'ticket-closeWithReason-'.$ticket->id,
            'style' => DiscordButton::Danger,
            'label' => 'Close With Reason',
            'emoji' => ['name' => '🔒'],
        ];

        $response = Http::discordBot()->post('/channels/'.$ticket->channel_id.'/messages', [
            'embeds' => [
                [
                    'description' => $ticket->ticketButton?->initial_message,
                    'color' => hexdec('22e629'), // Green
                ],
            ],
            'components' => [
                [
                    'type' => 1,
                    'components' => [$closeButton, $closeWithReasonButton],
                ],
            ],
        ]);

        return $response->json();
    }

    public function sendTranscript(Ticket $ticket): bool
    {
        $guildId = config('services.discord.server_id');
        /**
         * @var TicketConfig $ticketConfig
         */
        $ticketConfig = TicketConfig::where('guild_id', $guildId)->first();

        $embed = [
            'title' => 'Ticket Closes',
            'color' => hexdec('22e629'), // Green
            'fields' => [
                [
                    'name' => 'Ticket ID',
                    'value' => $ticket->id,
                    'inline' => true,
                ],
                [
                    'name' => 'Opened By',
                    'value' => '<@'.$ticket->created_by_discord_user_id.'>',
                    'inline' => true,
                ],
                [
                    'name' => 'Closed By',
                    'value' => '<@'.$ticket->closed_by_discord_user_id.'>',
                    'inline' => true,
                ],
                [
                    'name' => 'Open Time',
                    'value' => '<t:'.$ticket->created_at?->timestamp.'>',
                    'inline' => true,
                ],
                [
                    'name' => 'Reason',
                    'value' => $ticket->closed_reason ?? '---',
                ],
            ],
        ];
        $response = Http::discordBot()->post('/channels/'.$ticketConfig->transcript_channel_id.'/messages', [
            'embeds' => [$embed],
        ]);

        return $response->ok();
    }
}
