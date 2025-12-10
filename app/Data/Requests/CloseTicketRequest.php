<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CloseTicketRequest extends Data
{
    public function __construct(
        #[Max(20)]
        public readonly string $closed_by_discord_user_id,
        public readonly ?string $closed_reason,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticket.update');
    }
}
