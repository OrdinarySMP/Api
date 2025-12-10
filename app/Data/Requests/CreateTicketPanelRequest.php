<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CreateTicketPanelRequest extends Data
{
    public function __construct(
        #[Max(100)]
        public readonly string $title,
        #[Max(1000)]
        public readonly string $message,
        #[Max(7)]
        public readonly string $embed_color,
        #[Max(20)]
        public readonly string $channel_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketPanel.create');
    }
}
