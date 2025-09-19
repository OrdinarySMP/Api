<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationQuestion;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationResponse;
use App\Models\ApplicationSubmission;
use App\Repositories\ApplicationActivityRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->repo = new ApplicationActivityRepository;
    ApplicationSubmission::unsetEventDispatcher();
});

it('sends log when a question answer is created', function () {
    Http::fake();

    $application = Application::factory()->create([
        'name' => 'Test Application',
        'activity_channel' => '123456789',
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '987654321',
        'application_id' => $application->id,
    ]);
    $question = ApplicationQuestion::factory()->create([
        'question' => 'What is your name?',
    ]);
    $answer = ApplicationQuestionAnswer::factory()->create([
        'answer' => 'John Doe',
        'application_question_id' => $question->id,
        'application_submission_id' => $submission->id,
    ]);

    $this->repo->questionAnswerCreated($answer);

    Http::assertSent(function ($request) use ($answer, $application, $submission, $question) {
        return str_contains($request['content'], $answer->answer)
            && str_contains($request['content'], $application->name)
            && str_contains($request['content'], $question->question)
            && str_contains($request['content'], "<@{$submission->discord_id}>");
    });
});

it('sends log when submission status is updated', function () {
    Http::fake();

    $application = Application::factory()->create([
        'name' => 'Test Application',
        'activity_channel' => '123456789',
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '111222333',
        'application_id' => $application->id,
        'state' => ApplicationSubmissionState::Accepted,
    ]);

    $this->repo->submissionStatusUpdate($submission);

    Http::assertSent(function ($request) use ($submission, $application) {
        return str_contains($request['content'], $application->name)
            && str_contains($request['content'], $submission->state->label());
    });
});

it('handles missing activity channel gracefully', function () {
    Http::fake();

    $application = Application::factory()->create([
        'name' => 'Test Application',
        'activity_channel' => null,
    ]);
    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '444555666',
        'application_id' => $application->id,
    ]);

    $result = $this->repo->submissionCreated($submission);

    Http::assertNothingSent();
});

it('uses custom_response if present', function () {
    Http::fake();
    $application = Application::factory()->create([
        'name' => 'Test App',
        'activity_channel' => '123456789',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '123123123',
        'application_id' => $application->id,
        'custom_response' => 'My custom response',
        'state' => ApplicationSubmissionState::Denied,
    ]);

    $this->repo->submissionHandled($submission);

    Http::assertSent(fn ($request) => str_contains($request['content'], 'My custom response'));
});

it('uses applicationResponse if no custom_response', function () {
    Http::fake();
    $application = Application::factory()->create([
        'name' => 'Test App',
        'activity_channel' => '123456789',
    ]);

    $response = ApplicationResponse::factory()->create([
        'application_id' => $application->id,
        'response' => 'Stored response',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '555666777',
        'application_id' => $application->id,
        'state' => ApplicationSubmissionState::Denied,
        'custom_response' => null,
        'application_response_id' => $response->id,
    ]);

    $this->repo->submissionHandled($submission);

    Http::assertSent(fn ($request) => str_contains($request['content'], 'Stored response'));
});

it('uses accept_message when accepted', function () {
    Http::fake();
    $application = Application::factory()->create([
        'name' => 'Test App',
        'activity_channel' => '123456789',
        'accept_message' => 'Welcome aboard!',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '888999000',
        'application_id' => $application->id,
        'state' => ApplicationSubmissionState::Accepted,
        'custom_response' => null,
        'application_response_id' => null,
    ]);

    $this->repo->submissionHandled($submission);

    Http::assertSent(fn ($request) => str_contains($request['content'], 'Welcome aboard!'));
});

it('uses deny_message when denied and no other response', function () {
    Http::fake();
    $application = Application::factory()->create([
        'name' => 'Test App',
        'activity_channel' => '123456789',
        'deny_message' => 'Sorry, not this time.',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '111222333',
        'application_id' => $application->id,
        'state' => ApplicationSubmissionState::Denied,
        'custom_response' => null,
        'application_response_id' => null,
    ]);

    $this->repo->submissionHandled($submission);

    Http::assertSent(fn ($request) => str_contains($request['content'], 'Sorry, not this time.'));
});

it('falls back to --- when nothing else is available', function () {
    Http::fake();
    $application = Application::factory()->create([
        'name' => 'Test App',
        'activity_channel' => '123456789',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '444555666',
        'application_id' => $application->id,
        'state' => ApplicationSubmissionState::Pending,
        'custom_response' => null,
        'application_response_id' => null,
    ]);

    $this->repo->submissionHandled($submission);

    Http::assertSent(fn ($request) => str_contains($request['content'], '---'));
});

it('logs error when http client throws exception', function () {
    Http::fake(function () {
        throw new Exception('connection failed');
    });

    Log::spy();

    $application = Application::factory()->create([
        'name' => 'Test Application',
        'activity_channel' => '123456789',
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'discord_id' => '111222333',
        'application_id' => $application->id,
    ]);

    $this->repo->submissionCreated($submission);

    Log::shouldHaveReceived('error')->once();
});
