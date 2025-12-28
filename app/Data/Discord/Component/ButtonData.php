<?php

declare(strict_types=1);

namespace App\Data\Discord\Component;

use App\Enums\DiscordButton;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordButtonData')]
class ButtonData extends Data
{
    public function __construct(
        public readonly DiscordButton $style,
        public readonly string $label,
        public readonly int $type = 2,
        public readonly ?string $custom_id = null,
        public readonly ?EmojiData $emoji = null,
        public readonly ?string $url = null,
    ) {}

    public static function success(string $custom_id, string $label, ?EmojiData $emoji = null): self
    {
        return new self(
            custom_id: $custom_id,
            style: DiscordButton::Success,
            label: $label,
            emoji: $emoji,
        );
    }

    public static function danger(string $custom_id, string $label, ?EmojiData $emoji = null): self
    {
        return new self(
            custom_id: $custom_id,
            style: DiscordButton::Danger,
            label: $label,
            emoji: $emoji,
        );
    }

    public static function primary(string $custom_id, string $label, ?EmojiData $emoji = null): self
    {
        return new self(
            custom_id: $custom_id,
            style: DiscordButton::Primary,
            label: $label,
            emoji: $emoji,
        );
    }

    public static function link(string $label, string $url): self
    {
        return new self(
            style: DiscordButton::Link,
            label: $label,
            url: $url,
        );
    }
}
