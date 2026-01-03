<?php

namespace App\Data\Requests;

use App\Models\ApplicationQuestion;
use App\Models\ApplicationSubmission;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class CreateApplicationQuestionAnswerRequest extends Data
{
    public function __construct(
        #[Exists(ApplicationQuestion::class, 'id')]
        public readonly int $application_question_id,
        #[Exists(ApplicationSubmission::class, 'id')]
        public readonly int $application_submission_id,
        public readonly string $answer,
        public readonly ?string $attachments,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationAnswerQuestion.create');
    }
}
