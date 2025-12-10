<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Data;

class ResendServerContentRequest extends Data
{
    public function __construct(
        public readonly string $channel_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('serverContent.resend');
    }
}
