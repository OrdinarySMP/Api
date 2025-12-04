<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\Discord\UserData;
use App\Models\TicketTranscript;
use App\Repositories\DiscordRepository;
use App\Repositories\TicketTranscriptRepository;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketTranscriptData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_id,
        public readonly string $discord_user_id,
        public readonly string $message_id,
        public readonly ?string $message,
        public readonly ?string $attachments,
        public readonly ?string $embeds,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly ?Carbon $deleted_at,
        public readonly ?UserData $user,
    ) {}

    public static function fromTicketTranscript(TicketTranscript $ticketTranscript): self
    {
        $ticketTranscriptRepository = new TicketTranscriptRepository(new DiscordRepository);

        return new self(
            $ticketTranscript->id,
            $ticketTranscript->ticket_id,
            $ticketTranscript->discord_user_id,
            $ticketTranscript->message_id,
            $ticketTranscript->message,
            $ticketTranscript->attachments,
            $ticketTranscript->embeds,
            $ticketTranscript->created_at,
            $ticketTranscript->updated_at,
            $ticketTranscript->deleted_at,
            $ticketTranscriptRepository->getUser($ticketTranscript),
        );
    }
}
