<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\DiscordButton;
use App\Models\TicketButton;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketButtonData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_team_id,
        public readonly int $ticket_panel_id,
        public readonly string $text,
        public readonly DiscordButton $color,
        public readonly string $initial_message,
        public readonly string $emoji,
        public readonly string $naming_scheme,
        public readonly bool $disabled,
        /** @var ?Collection<array-key, TicketButtonPingRoleData> */
        #[LiteralTypeScriptType('TicketButtonPingRoleData[]')]
        public readonly ?Collection $ticket_button_ping_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly ?Collection $ticket_button_ping_role_ids,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly Lazy|TicketTeamData $ticket_team,
        public readonly Lazy|TicketPanelData $ticket_panel,
    ) {}

    public static function fromTicketButton(TicketButton $ticketButton): self
    {
        return new self(
            $ticketButton->id,
            $ticketButton->ticket_team_id,
            $ticketButton->ticket_panel_id,
            $ticketButton->text,
            $ticketButton->color,
            $ticketButton->initial_message,
            $ticketButton->emoji,
            $ticketButton->naming_scheme,
            $ticketButton->disabled,
            $ticketButton->relationLoaded('ticketButtonPingRoles') ? TicketButtonPingRoleData::collect($ticketButton->ticketButtonPingRoles) : null,
            $ticketButton->relationLoaded('ticketButtonPingRoles') ? TicketButtonPingRoleData::collect($ticketButton->ticketButtonPingRoles)->pluck('role_id') : null,
            $ticketButton->created_at,
            $ticketButton->updated_at,
            Lazy::whenLoaded('ticketTeam', $ticketButton, fn() => TicketTeamData::from($ticketButton->ticketTeam)),
            Lazy::whenLoaded('ticketPanel', $ticketButton, fn() => TicketPanelData::from($ticketButton->ticketPanel)),
        );
    }
}
