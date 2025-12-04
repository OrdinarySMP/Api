<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Rule;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RuleData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $number,
        public readonly string $name,
        public readonly string $rule,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromFaq(Rule $rule): self
    {
        return new self(
            $rule->id,
            $rule->number,
            $rule->name,
            $rule->rule,
            $rule->created_at,
            $rule->updated_at,
        );
    }
}
