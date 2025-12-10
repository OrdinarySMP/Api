<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Container\Attributes\RouteParameter;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class UpdateUserRequest extends Data
{
    public function __construct(
        #[Password(min: 16, letters: true, mixedCase: true, numbers: true, symbols: true, uncompromised: true), Confirmed()]
        public readonly string $password,
    ) {}

    public static function authorize(
        #[CurrentUser] User $currentUser,
        #[RouteParameter('user')] User $user
    ): bool {
        return $currentUser->id === $user->id;
    }
}
