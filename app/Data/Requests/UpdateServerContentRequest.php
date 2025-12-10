<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateServerContentRequest extends Data
{
    public function __construct(
        #[Max(128)]
        public readonly Optional|string $name,
        #[Url(['http', 'https'])]
        public readonly Optional|string $url,
        #[Max(512)]
        public readonly Optional|string $description,
        public readonly Optional|bool $is_recommended,
        public readonly Optional|bool $is_active,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('serverContent.update');
    }
}
