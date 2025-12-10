<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class CreateRuleRequest extends Data
{
    public function __construct(
        #[Min(1)]
        public readonly int $number,
        #[Max(20)]
        public readonly string $name,
        #[Max(4095)]
        public readonly string $rule,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('rule.create');
    }
}
