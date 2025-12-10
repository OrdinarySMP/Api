<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Data;

class ReadTicketConfigRequest extends Data
{
    public function __construct() {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketConfig.read');
    }
}
