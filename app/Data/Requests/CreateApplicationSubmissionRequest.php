<?php

namespace App\Data\Requests;

use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

/**
 * @property int<0, max>|null $application_response_id
 */
class CreateApplicationSubmissionRequest extends Data
{
    public function __construct(

        public readonly string $discord_id,
        #[WithCast(DateTimeInterfaceCast::class, ['format' => 'Y-m-d H:i:s'])]
        public readonly ?Carbon $submitted_at,
        #[Exists(ApplicationResponse::class, 'id')]
        public readonly ?int $application_response_id,
        public readonly ApplicationSubmissionState $state,
        public readonly ?string $custom_response,
        public readonly ?string $handled_by,
        #[Exists(Application::class, 'id')]
        public readonly int $application_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('applicationSubmission.create');
    }
}
