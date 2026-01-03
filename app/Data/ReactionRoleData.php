<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ReactionRole;
use App\Repositories\DiscordRepository;
use App\Rules\DiscordMessageRule;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReactionRoleData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $message_id,
        public readonly string $channel_id,
        public readonly string $emoji,
        public readonly string $role_id,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly string $role_name,
        public readonly string $message_link,
    ) {}

    public static function fromFaq(ReactionRole $reactionRole): self
    {
        $discordRepository = new DiscordRepository;

        $role = $discordRepository->roles()?->first(fn ($role) => $role->id === $reactionRole->role_id);

        return new self(
            $reactionRole->id,
            $reactionRole->message_id,
            $reactionRole->channel_id,
            $reactionRole->emoji,
            $reactionRole->role_id,
            $reactionRole->created_at,
            $reactionRole->updated_at,
            $role->name ?? 'role-not-found',
            DiscordMessageRule::$discordChannelLinkBase.config('services.discord.server_id').'/'.$reactionRole->channel_id.'/'.$reactionRole->message_id,
        );
    }
}
