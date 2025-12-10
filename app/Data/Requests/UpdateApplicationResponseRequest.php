<?php

namespace App\Data\Requests;

use App\Enums\ApplicationResponseType;
use App\Models\Application;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateApplicationResponseRequest extends Data
{
    public function __construct(
        public readonly Optional|ApplicationResponseType $type,
        public readonly Optional|string $name,
        public readonly Optional|string $response,
        #[Exists(Application::class, 'id')]
        public readonly Optional|int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationResponse.update');
    }
}
