<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ApplicationQuestion;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationQuestionData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $order,
        public readonly int $application_id,
        public readonly string $question,
        public readonly bool $is_active,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromApplicationQuestion(ApplicationQuestion $applicationQuestion): self
    {
        return new self(
            $applicationQuestion->id,
            $applicationQuestion->order,
            $applicationQuestion->application_id,
            $applicationQuestion->question,
            $applicationQuestion->is_active,
            $applicationQuestion->created_at,
            $applicationQuestion->updated_at,
        );
    }
}
