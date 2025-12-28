<?php

namespace App\Repositories;

use App\Data\Discord\Component\ActionRowData;
use App\Data\Discord\Component\ButtonData;
use App\Data\Discord\Component\EmojiData;
use App\Data\Discord\Embed\EmbedData;
use App\Data\Discord\Embed\FieldsData;
use App\Data\Requests\CreateTicketRequest;
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

    public function createForButton(TicketButton $ticketButton, CreateTicketRequest $request): Ticket
    {
        $roles = $this->discordRepository->roles();
        /**
         * @var array{id:string} $everyoneRole
         */
        $everyoneRole = $roles->firstWhere('name', '@everyone');

        $ticket = Ticket::create([
            'ticket_button_id' => $request->ticket_button_id,
            'created_by_discord_user_id' => $request->created_by_discord_user_id,
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
                    'id' => $request->created_by_discord_user_id,
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
        $buttons = collect([
            ButtonData::danger(
                'ticket-close-'.$ticket->id,
                'Close',
                new EmojiData('🔒'),
            ),
            ButtonData::danger(
                'ticket-closeWithReason-'.$ticket->id,
                'Close With Reason',
                new EmojiData('🔒'),
            ),
        ]);

        $response = Http::discordBot()->post('/channels/'.$ticket->channel_id.'/messages', [
            'embeds' => [
                new EmbedData(
                    description: $ticket->ticketButton?->initial_message,
                    color: (string) hexdec('22e629'), // Green
                ),
            ],
            'components' => [
                new ActionRowData(components: $buttons),
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

        $buttons = collect([
            ButtonData::link(
                'Transcript',
                config('services.frontend.base_url').'/ticket/transcript/'.$ticket->id,
            ),
        ]);

        $embed = new EmbedData(
            title: 'Ticket Closes',
            color: (string) hexdec('22e629'), // Green
            fields: collect([
                new FieldsData(
                    name: 'Ticket ID',
                    value: (string) $ticket->id,
                    inline: true,
                ),
                new FieldsData(
                    name: 'Opened By',
                    value: '<@'.$ticket->created_by_discord_user_id.'>',
                    inline: true,
                ),
                new FieldsData(
                    name: 'Closed By',
                    value: '<@'.$ticket->closed_by_discord_user_id.'>',
                    inline: true,
                ),
                new FieldsData(
                    name: 'Ticket type',
                    value: $ticket->ticketButton->text ?? '---',
                    inline: true,
                ),
                new FieldsData(
                    name: 'Open Time',
                    value: '<t:'.$ticket->created_at?->timestamp.'>',
                    inline: true,
                ),
                new FieldsData(
                    name: 'Close Time',
                    value: '<t:'.$ticket->updated_at?->timestamp.'>',
                    inline: true,
                ),
                new FieldsData(
                    name: 'Reason',
                    value: $ticket->closed_reason ?? '---',
                ),
            ]),
        );
        $response = Http::discordBot()->post('/channels/'.$ticketConfig->transcript_channel_id.'/messages', [
            'embeds' => collect([$embed]),
            'components' => [
                new ActionRowData(components: $buttons),
            ],
        ]);

        return $response->ok();
    }
}
