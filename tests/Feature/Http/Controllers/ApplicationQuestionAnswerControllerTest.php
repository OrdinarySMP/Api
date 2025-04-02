<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationQuestion;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationSubmission;
use App\Models\User;

test('auth user can get application question answer', function () {
    $applicationQuestion = ApplicationQuestionAnswer::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application-question-answer.index'))
        ->assertOk()
        ->assertJson(['data' => [$applicationQuestion->toArray()]]);
});

test('can create application question answer', function () {
    $user = User::factory()->owner()->create();
    $applicationQuestion = ApplicationQuestion::factory()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create([
        'state' => ApplicationSubmissionState::Pending,
    ]);
    $data = [
        'application_question_id' => $applicationQuestion->id,
        'application_submission_id' => $applicationSubmission->id,
        'answer' => 'Test',
    ];

    $this->actingAs($user)
        ->postJson(route('application-question-answer.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('application_question_answers', $data);
});

test('can update application question answer', function () {
    $user = User::factory()->owner()->create();
    $applicationQuestionAnswer = ApplicationQuestionAnswer::factory()->create();
    $applicationQuestion = ApplicationQuestion::factory()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create();
    $data = [
        'application_question_id' => $applicationQuestion->id,
        'application_submission_id' => $applicationSubmission->id,
        'answer' => 'Test',
    ];

    $this->actingAs($user)
        ->patchJson(route('application-question-answer.update', $applicationQuestionAnswer), $data)
        ->assertOk()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('application_question_answers', $data);
});

test('can delete application question answer', function () {
    $user = User::factory()->owner()->create();
    $applicationQuestionAnswer = ApplicationQuestionAnswer::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application-question-answer.destroy', $applicationQuestionAnswer))
        ->assertOk();

    $this->assertDatabaseMissing('application_question_answers', $applicationQuestionAnswer->toArray());
});
