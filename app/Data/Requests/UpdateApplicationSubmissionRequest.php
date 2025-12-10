<?php

namespace App\Data\Requests;

use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateApplicationSubmissionRequest extends Data
{
    public function __construct(

        public readonly Optional|string $discord_id,
        #[DateFormat('Y-m-d H:i:s')]
        #[WithCast(DateTimeInterfaceCast::class, ['format' => 'Y-m-d H:i:s'])]
        public readonly Optional|Carbon $submitted_at,
        #[Exists(ApplicationResponse::class, 'id')]
        public readonly Optional|null|int $application_response_id,
        public readonly Optional|ApplicationSubmissionState $state,
        public readonly Optional|null|string $custom_response,
        public readonly Optional|null|string $handled_by,
        #[Exists(Application::class, 'id')]
        public readonly Optional|int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationSubmission.update');
    }
}
