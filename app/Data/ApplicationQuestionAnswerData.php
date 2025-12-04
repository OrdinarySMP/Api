<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ApplicationQuestionAnswer;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationQuestionAnswerData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $application_question_id,
        public readonly string $answer,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
        public readonly int $application_submission_id,
        public readonly ?string $attachments,
        public readonly ApplicationQuestionData $application_question,
    ) {}

    public static function fromApplicationQuestionAnswer(ApplicationQuestionAnswer $applicationQuestionAnswer): self
    {
        return new self(
            $applicationQuestionAnswer->id,
            $applicationQuestionAnswer->application_question_id,
            $applicationQuestionAnswer->answer,
            $applicationQuestionAnswer->created_at,
            $applicationQuestionAnswer->updated_at,
            $applicationQuestionAnswer->application_submission_id,
            $applicationQuestionAnswer->attachments,
            ApplicationQuestionData::from($applicationQuestionAnswer->applicationQuestion),
        );
    }
}
