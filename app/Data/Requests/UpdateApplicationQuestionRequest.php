<?php

namespace App\Data\Requests;

use App\Models\Application;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateApplicationQuestionRequest extends Data
{
    public function __construct(
        public readonly Optional|string $question,
        public readonly Optional|int $order,
        public readonly Optional|bool $is_active,
        #[Exists(Application::class, 'id')]
        public readonly Optional|int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationQuestion.update');
    }
}
