<?php

namespace App\Data\Requests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CreateTicketTranscriptRequest extends Data
{
    public function __construct(
        #[Exists(Ticket::class, 'id')]
        public readonly int $ticket_id,
        #[Max(20)]
        public readonly string $discord_user_id,
        #[Max(20)]
        public readonly string $message_id,
        public readonly ?string $message,
        #[Json]
        public readonly ?string $attachments,
        #[Json]
        public readonly ?string $embeds,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketTranscript.create');
    }
}
