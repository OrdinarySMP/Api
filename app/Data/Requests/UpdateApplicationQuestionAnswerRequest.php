<?php

namespace App\Data\Requests;

use App\Models\ApplicationQuestion;
use App\Models\ApplicationSubmission;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateApplicationQuestionAnswerRequest extends Data
{
    public function __construct(
        #[Exists(ApplicationQuestion::class, 'id')]
        public readonly Optional|int $application_question_id,
        #[Exists(ApplicationSubmission::class, 'id')]
        public readonly Optional|int $application_submission_id,
        public readonly Optional|string $answer,
        public readonly Optional|null|string $attachments,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationQuestionAnswer.update');
    }
}
