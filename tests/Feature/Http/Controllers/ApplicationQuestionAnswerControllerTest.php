<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationQuestion;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationSubmission;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        ApplicationQuestionAnswer::factory()->create();
        $this->assertReadPermissions('application-question-answer.index', 'applicationAnswerQuestion.read');
    });

    test('can read application question answer', function () {
        $applicationQuestion = ApplicationQuestionAnswer::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('application-question-answer.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $applicationQuestion->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $applicationQuestion = ApplicationQuestion::factory()->create();
        $applicationSubmission = ApplicationSubmission::factory()->create([
            'state' => ApplicationSubmissionState::Pending,
        ]);
        $data = [
            'application_question_id' => $applicationQuestion->id,
            'application_submission_id' => $applicationSubmission->id,
            'answer' => 'Test',
        ];
        $this->assertCreatePermissions('application-question-answer.store', 'applicationAnswerQuestion.create', $data, ApplicationQuestionAnswer::class);
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
});

describe('update operations', function () {
    test('update permission', function () {
        $applicationQuestionAnswer = ApplicationQuestionAnswer::factory()->create();
        $data = [
            'answer' => 'Test',
        ];
        $this->assertUpdatePermissions('application-question-answer.update', 'applicationAnswerQuestion.update', $applicationQuestionAnswer, $data, ApplicationQuestionAnswer::class);
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
});

describe('delete operations', function () {
    test('delete permission', function () {
        $applicationQuestionAnswer = ApplicationQuestionAnswer::factory()->create();
        $this->assertDeletePermissions('application-question-answer.destroy', 'applicationAnswerQuestion.delete', $applicationQuestionAnswer, ApplicationQuestionAnswer::class);
    });
    test('can delete application question answer', function () {
        $user = User::factory()->owner()->create();
        $applicationQuestionAnswer = ApplicationQuestionAnswer::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('application-question-answer.destroy', $applicationQuestionAnswer))
            ->assertOk();

        $this->assertDatabaseMissing('application_question_answers', $applicationQuestionAnswer->toArray());
    });
});
