<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateTicketTeamRequest extends Data
{
    public function __construct(
        #[Max(100)]
        public readonly Optional|string $name,
        /** @var ?array<array-key, string> */
        public readonly Optional|null|array $ticket_team_role_ids,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketTeam.update');
    }
}
