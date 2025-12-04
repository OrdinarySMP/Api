<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\Discord\UserData;
use App\Enums\TicketState;
use App\Models\Ticket;
use App\Repositories\DiscordRepository;
use App\Repositories\TicketRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_button_id,
        public readonly ?string $channel_id,
        public readonly TicketState $state,
        public readonly string $created_by_discord_user_id,
        public readonly ?string $closed_by_discord_user_id,
        public readonly ?string $closed_reason,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly string $channel_name,
        /** @var ?Collection<array-key, TicketTranscriptData> */
        #[LiteralTypeScriptType('TicketTranscriptData[]')]
        public readonly ?Collection $ticket_transcripts,
        public readonly ?UserData $created_by_discord_user,
        public readonly ?UserData $closed_by_discord_user,
        public readonly ?TicketButtonData $ticket_button,
    ) {}

    public static function fromTicket(Ticket $ticket): self
    {
        $discordRepository = new DiscordRepository;
        $ticketRepository = new TicketRepository($discordRepository);

        return new self(
            $ticket->id,
            $ticket->ticket_button_id,
            $ticket->channel_id,
            $ticket->state,
            $ticket->created_by_discord_user_id,
            $ticket->closed_by_discord_user_id,
            $ticket->closed_reason,
            $ticket->created_at,
            $ticket->updated_at,
            $ticketRepository->getChannelName($ticket),
            $ticket->relationLoaded('ticketTranscripts') ? TicketTranscriptData::collect($ticket->ticketTranscripts) : null,
            $discordRepository->getUserById($ticket->created_by_discord_user_id),
            $ticket->closed_by_discord_user_id ?
                $discordRepository->getUserById($ticket->closed_by_discord_user_id) :
                null,
            $ticket->relationLoaded('ticketButton') ? TicketButtonData::from($ticket->ticketButton) : null,
        );
    }
}
