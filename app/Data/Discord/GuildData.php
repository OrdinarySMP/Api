<?php

declare(strict_types=1);

namespace App\Data\Discord;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordGuildData')]
class GuildData extends Data
{
    public function __construct(
        public readonly ?string $identity_guild_id,
        public readonly ?string $identity_enabled,
        public readonly ?string $tag,
        public readonly ?string $badge,
    ) {}
}
