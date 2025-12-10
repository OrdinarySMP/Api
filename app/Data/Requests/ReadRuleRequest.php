<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Data;

class ReadRuleRequest extends Data
{
    public function __construct() {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('rule.read');
    }
}
