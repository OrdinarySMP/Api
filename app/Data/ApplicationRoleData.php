<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ApplicationRoleType;
use App\Models\ApplicationRole;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationRoleData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $application_id,
        public readonly string $role_id,
        public readonly ApplicationRoleType $type,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromApplicationRole(ApplicationRole $applicationRole): self
    {
        return new self(
            $applicationRole->id,
            $applicationRole->application_id,
            $applicationRole->role_id,
            $applicationRole->type,
            $applicationRole->created_at,
            $applicationRole->updated_at,
        );
    }
}
