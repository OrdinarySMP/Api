<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CreateServerContentMessageRequest extends Data
{
    public function __construct(
        #[Max(2000)]
        public readonly string $heading,
        #[Max(2000)]
        public readonly string $not_recommended,
        #[Max(2000)]
        public readonly string $recommended,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('serverContentMessage.create');
    }
}
