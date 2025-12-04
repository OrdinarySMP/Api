<?php

namespace App\Data\Requests;

use App\Models\TicketButton;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CreateTicketRequest extends Data
{
    public function __construct(
        #[Exists(TicketButton::class, 'id')]
        public readonly int $ticket_button_id,
        #[Max(20)]
        public readonly string $created_by_discord_user_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->hasRole('Bot');
    }
}
