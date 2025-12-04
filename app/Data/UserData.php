<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $discord_id,
        public readonly ?string $nickname,
        public readonly string $name,
        public readonly string $avatar,
        public readonly bool $is_owner,
        /** @var Collection<int, string> $permissions */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $permissions,
    ) {}

    public static function fromUser(User $user, bool $isOwner = false): self
    {
        return new self(
            $user->id,
            $user->discord_id,
            $user->nickname,
            $user->name,
            $user->avatar,
            $isOwner,
            $user->getPermissionsViaRoles()->pluck('name'),
        );
    }
}
