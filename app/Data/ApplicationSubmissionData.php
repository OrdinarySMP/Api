<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\Discord\MemberData;
use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationSubmission;
use App\Repositories\DiscordRepository;
use Carbon\Carbon as CCarbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationSubmissionData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $discord_id,
        public readonly ?CCarbon $submitted_at,
        public readonly ApplicationSubmissionState $state,
        public readonly ?int $application_response_id,
        public readonly ?string $custom_response,
        public readonly ?string $message_id,
        public readonly ?string $channel_id,
        public readonly ?string $handled_by,
        public readonly ?int $application_id,
        public readonly ?ApplicationData $application,
        /** @var ?Collection<array-key, ApplicationQuestionAnswerData> */
        #[LiteralTypeScriptType('ApplicationQuestionAnswerData[]')]
        public readonly ?Collection $application_question_answers,
        public readonly Lazy|ApplicationResponseData $application_response,
        public readonly ?CCarbon $created_at,
        public readonly ?CCarbon $updated_at,
        public readonly ?MemberData $member,
    ) {}

    public static function fromApplicationSubmission(ApplicationSubmission $applicationSubmission): self
    {
        $discordRepository = new DiscordRepository;
        $member = null;
        try {
            $member = $discordRepository->getGuildMemberById($applicationSubmission->discord_id);
        } catch (\Error $e) {
            $member = null;
        }

        return new self(
            $applicationSubmission->id,
            $applicationSubmission->discord_id,
            $applicationSubmission->submitted_at,
            $applicationSubmission->state,
            $applicationSubmission->application_response_id,
            $applicationSubmission->custom_response,
            $applicationSubmission->message_id,
            $applicationSubmission->channel_id,
            $applicationSubmission->handled_by,
            $applicationSubmission->application_id,
            $applicationSubmission->application ? ApplicationData::from($applicationSubmission->application) : null,
            ApplicationQuestionAnswerData::collect($applicationSubmission->applicationQuestionAnswers),
            Lazy::whenLoaded('applicationResponse', $applicationSubmission, fn () => ApplicationResponseData::from($applicationSubmission->applicationResponse)),
            $applicationSubmission->created_at,
            $applicationSubmission->updated_at,
            $member,
        );
    }
}
