<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class UpdateTicketConfigRequest extends Data
{
    public function __construct(
        #[Max(20)]
        public readonly string $category_id,
        #[Max(20)]
        public readonly string $transcript_channel_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketConfig.create');
    }
}
