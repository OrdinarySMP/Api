<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class SetupTicketConfigRequest extends Data
{
    public function __construct(
        #[Max(20)]
        public readonly string $category_id,
        #[Max(20)]
        public readonly string $transcript_channel_id,
        #[Max(20)]
        public readonly string $create_channel_id,
        #[Max(20), Unique('ticket_configs', 'guild_id')]
        public readonly string $guild_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketConfig.setup');
    }
}
