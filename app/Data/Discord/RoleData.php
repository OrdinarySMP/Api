<?php

declare(strict_types=1);

namespace App\Data\Discord;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordRoleData')]
class RoleData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {}
}
