<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TicketPanel;
use App\Repositories\DiscordRepository;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketPanelData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $message,
        public readonly string $embed_color,
        public readonly string $channel_id,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly string $channel_name,
    ) {}

    public static function fromTicketPanel(TicketPanel $ticketPanel): self
    {
        $discordRepository = new DiscordRepository;
        $channel = $discordRepository->textChannels()->first(fn ($textChannel) => $textChannel['id'] === $ticketPanel->channel_id);

        return new self(
            $ticketPanel->id,
            $ticketPanel->title,
            $ticketPanel->message,
            $ticketPanel->embed_color,
            $ticketPanel->channel_id,
            $ticketPanel->created_at,
            $ticketPanel->updated_at,
            $channel ? $channel['name'] : 'channel-not-found',
        );
    }
}
