<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TicketTeamRole;
use App\Repositories\DiscordRepository;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketTeamRoleData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_team_id,
        public readonly string $role_id,
        public readonly string $role_name,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromTicketTeamRole(TicketTeamRole $ticketTeamRole): self
    {
        $discordRepository = new DiscordRepository;

        $role = $discordRepository->roles()?->first(fn ($role) => $role->id === $ticketTeamRole->role_id);

        return new self(
            $ticketTeamRole->id,
            $ticketTeamRole->ticket_team_id,
            $ticketTeamRole->role_id,
            $role->name ?? 'role-not-found',
            $ticketTeamRole->created_at,
            $ticketTeamRole->updated_at,
        );
    }
}
