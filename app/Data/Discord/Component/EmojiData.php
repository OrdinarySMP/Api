<?php

declare(strict_types=1);

namespace App\Data\Discord\Component;

use App\Models\TicketButton;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordEmojiData')]
class EmojiData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $id = null,
    ) {}

    public static function fromTicketButton(TicketButton $ticketButton): self
    {
        if (str_contains($ticketButton->emoji, '<') && str_contains($ticketButton->emoji, '>')) {
            $discordEmoji = str_replace('<', '', $ticketButton->emoji);
            $discordEmoji = str_replace('>', '', $discordEmoji);
            [$emojiName, $emojiId] = explode(':', $discordEmoji);

            return new self(
                name: $emojiName,
                id: $emojiId,
            );
        }

        return new self(
            name: $ticketButton->emoji,
        );
    }
}
