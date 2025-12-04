<?php

declare(strict_types=1);

namespace App\Data\Discord;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordUserData')]
class UserData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?string $avatar,
        public readonly string $discriminator,
        public readonly int $public_flags,
        public readonly int $flags,
        public readonly ?string $banner,
        public readonly ?int $accent_color,
        public readonly ?string $global_name,
        public readonly ?string $avatar_decoration_data,
        public readonly ?string $collectibles,
        public readonly ?string $display_name_styles,
        public readonly ?string $banner_color,
        public readonly ?GuildData $clan,
        public readonly ?GuildData $primary_guild,
    ) {}
}
