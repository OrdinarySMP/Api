<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateFaqRequest extends Data
{
    public function __construct(
        #[Max(100)]
        public readonly Optional|string $question,
        #[Max(4095)]
        public readonly Optional|string $answer,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('faq.update');
    }
}
