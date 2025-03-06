<?php

namespace App\Repositories;

use App\Models\TicketPanel;
use Illuminate\Support\Facades\Http;

class TicketPanelRepository
{
    public function sendPanel(TicketPanel $ticketPanel): bool
    {
        $buttons = $ticketPanel->ticketButtons()->where('disabled', false)->get();
        if ($buttons->isEmpty()) {
            return false;
        }
        /**
         * @var mixed $components
         */
        $components = $buttons->map(function ($ticketButton): array {
            $emoji = ['name' => $ticketButton->emoji];
            if (str_contains($ticketButton->emoji, '<') && str_contains($ticketButton->emoji, '>')) {
                $discordEmoji = str_replace('<', '', $ticketButton->emoji);
                $discordEmoji = str_replace('>', '', $discordEmoji);
                [$emojiName, $emojiId] = explode(':', $discordEmoji);
                $emoji = [
                    'name' => $emojiName,
                    'id' => $emojiId,
                ];
            }

            return [
                'type' => 2, // button
                'custom_id' => 'ticket-create-'.$ticketButton->id,
                'style' => $ticketButton->color->value,
                'label' => $ticketButton->text,
                'emoji' => $emoji,
            ];
        });

        $data = [
            'embeds' => [
                [
                    'title' => $ticketPanel->title,
                    'description' => $ticketPanel->message,
                    'color' => hexdec(str_replace('#', '', $ticketPanel->embed_color)),
                ],
            ],
            'components' => [
                [
                    'type' => 1,
                    'components' => $components,
                ],
            ],
        ];

        $response = Http::discordBot()->post('/channels/'.$ticketPanel->channel_id.'/messages', $data);

        return $response->ok();
    }
}
