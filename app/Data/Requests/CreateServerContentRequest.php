<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class CreateServerContentRequest extends Data
{
    public function __construct(
        #[Max(128)]
        public readonly string $name,
        #[Url(['http', 'https'])]
        public readonly string $url,
        #[Max(512)]
        public readonly string $description,
        public readonly bool $is_recommended,
        public readonly bool $is_active,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('serverContent.create');
    }
}
