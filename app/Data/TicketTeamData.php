<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TicketTeam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketTeamData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** @var Collection<array-key, TicketTeamRoleData> */
        #[LiteralTypeScriptType('TicketTeamRoleData[]')]
        public readonly ?Collection $ticket_team_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly ?Collection $ticket_team_role_ids,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromTicketTeam(TicketTeam $ticketTeam): self
    {
        return new self(
            $ticketTeam->id,
            $ticketTeam->name,
            $ticketTeam->relationLoaded('ticketTeamRoles') ? TicketTeamRoleData::collect($ticketTeam->ticketTeamRoles) : null,
            $ticketTeam->relationLoaded('ticketTeamRoles') ? TicketTeamRoleData::collect($ticketTeam->ticketTeamRoles)->pluck('role_id') : null,
            $ticketTeam->created_at,
            $ticketTeam->updated_at,
        );
    }
}
