<?php

namespace App\Data\Requests;

use App\Models\Application;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class CreateApplicationQuestionRequest extends Data
{
    public function __construct(
        public readonly string $question,
        public readonly int $order,
        public readonly bool $is_active,
        #[Exists(Application::class, 'id')]
        public readonly int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationQuestion.create');
    }
}
