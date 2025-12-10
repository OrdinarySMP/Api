<?php

namespace App\Data\Requests;

use App\Enums\ApplicationResponseType;
use App\Models\Application;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class CreateApplicationResponseRequest extends Data
{
    public function __construct(
        public readonly ApplicationResponseType $type,
        public readonly string $name,
        public readonly string $response,
        #[Exists(Application::class, 'id')]
        public readonly int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationResponse.create');
    }
}
