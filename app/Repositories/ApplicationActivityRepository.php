<?php

namespace App\Repositories;

use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApplicationActivityRepository
{
    public function questionAnswerCreated(ApplicationQuestionAnswer $applicationQuestionAnswer): void
    {
        $question = $applicationQuestionAnswer->applicationQuestion;
        $submission = $applicationQuestionAnswer->applicationSubmission;
        $application = $submission?->application;
        $this->sendLog("<@{$submission?->discord_id}> answered the question `{$question?->question}` for `{$application?->name}`\n```{$applicationQuestionAnswer->answer}```", $application?->activity_channel);
    }

    public function submissionStatusUpdate(ApplicationSubmission $applicationSubmission): void
    {
        $application = $applicationSubmission->application;
        $this->sendLog("The status of the Application from <@{$applicationSubmission->discord_id}> for `{$application?->name}` was changed to `{$applicationSubmission->state->label()}`", $application?->activity_channel);
    }

    public function submissionCreated(ApplicationSubmission $applicationSubmission): void
    {
        $application = $applicationSubmission->application;
        $this->sendLog("A new Application was started by <@{$applicationSubmission->discord_id}> for `{$application?->name}`", $application?->activity_channel);
    }

    public function submissionCompleted(ApplicationSubmission $applicationSubmission): void
    {
        $application = $applicationSubmission->application;
        $this->sendLog("<@{$applicationSubmission->discord_id}> finished the application for `{$application?->name}`", $application?->activity_channel);
    }

    public function submissionHandled(ApplicationSubmission $applicationSubmission): void
    {
        $application = $applicationSubmission->application;
        $response = '---';
        if ($applicationSubmission->custom_response) {
            $response = $applicationSubmission->custom_response;
        } elseif ($applicationSubmission->applicationResponse?->response) {
            $response = $applicationSubmission->applicationResponse->response;
        } elseif ($applicationSubmission->state === ApplicationSubmissionState::Accepted) {
            $response = $application?->accept_message;
        } elseif ($applicationSubmission->state === ApplicationSubmissionState::Denied) {
            $response = $application?->deny_message;
        }

        $this->sendLog("<@{$applicationSubmission->discord_id}>'s application was {$applicationSubmission->state->label()} reason:\n```{$response}```", $application?->activity_channel);
    }

    public function sendLog(string $message, ?string $activityChannel): bool
    {
        if (! $activityChannel) {
            return true;
        }
        try {
            $response = Http::discordBot()->post("/channels/{$activityChannel}/messages", [
                'content' => $message,
            ]);

            return $response->ok();
        } catch (\Exception $e) {
            Log::error('Could not send application log: '.$e);

            return false;
        }

    }
}
