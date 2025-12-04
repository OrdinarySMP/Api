<?php

declare(strict_types=1);

namespace App\Data\Discord;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordMemberData')]
class MemberData extends Data
{
    public function __construct(
        public readonly ?string $avatar,
        public readonly ?string $banner,
        public readonly ?string $communication_disabled_until,
        public readonly int $flags,
        public readonly ?string $joined_at,
        public readonly ?string $nick,
        public readonly ?bool $pending,
        public readonly ?string $premium_since,
        /** @var string[] */
        public readonly array $roles,
        public readonly ?string $unusual_dm_activity_until,
        public readonly ?UserData $user,
        public readonly bool $mute,
        public readonly bool $deaf,
    ) {}
}
