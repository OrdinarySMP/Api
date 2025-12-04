<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Faq;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FaqData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $question,
        public readonly string $answer,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromFaq(Faq $faq): self
    {
        return new self(
            $faq->id,
            $faq->question,
            $faq->answer,
            $faq->created_at,
            $faq->updated_at,
        );
    }
}
