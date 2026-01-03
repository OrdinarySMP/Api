<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TicketButtonPingRole;
use App\Repositories\DiscordRepository;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketButtonPingRoleData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_button_id,
        public readonly string $role_id,
        public readonly string $role_name,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromTicketButtonPingRole(TicketButtonPingRole $ticketButtonPingRole): self
    {
        $discordRepository = new DiscordRepository;
        $role = $discordRepository->roles()?->first(fn ($role) => $role->id === $ticketButtonPingRole->role_id);

        return new self(
            $ticketButtonPingRole->id,
            $ticketButtonPingRole->ticket_button_id,
            $ticketButtonPingRole->role_id,
            $role->name ?? 'role-not-found',
            $ticketButtonPingRole->created_at,
            $ticketButtonPingRole->updated_at,
        );
    }
}
