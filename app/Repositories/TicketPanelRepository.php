<?php

namespace App\Repositories;

use App\Data\Discord\Component\ActionRowData;
use App\Data\Discord\Component\ButtonData;
use App\Data\Discord\Component\EmojiData;
use App\Data\Discord\Embed\EmbedData;
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

        $components = $buttons->map(function ($ticketButton): ButtonData {
            return new ButtonData(
                custom_id: 'ticket-create-'.$ticketButton->id,
                style: $ticketButton->color,
                label: $ticketButton->text,
                emoji: EmojiData::from($ticketButton),
            );
        });

        $data = [
            'embeds' => [
                new EmbedData(
                    title: $ticketPanel->title,
                    description: $ticketPanel->message,
                    color: (string) hexdec(str_replace('#', '', $ticketPanel->embed_color)),
                ),
            ],
            'components' => [
                new ActionRowData(components: $components),
            ],
        ];

        $response = Http::discordBot()->post('/channels/'.$ticketPanel->channel_id.'/messages', $data);

        return $response->ok();
    }
}
