<?php

use App\Models\Application;
use App\Models\ApplicationQuestion;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        ApplicationQuestion::factory()->create();
        $this->assertReadPermissions('application-question.index', 'applicationQuestion.read');
    });

    test('can read application question', function () {
        $applicationQuestion = ApplicationQuestion::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('application-question.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $applicationQuestion->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $application = Application::factory()->create();
        $data = [
            'question' => 'Test',
            'order' => 1,
            'is_active' => true,
            'application_id' => $application->id,
        ];
        $this->assertCreatePermissions('application-question.store', 'applicationQuestion.create', $data, ApplicationQuestion::class);
    });

    test('can create application question', function () {
        $user = User::factory()->owner()->create();
        $application = Application::factory()->create();
        $data = [
            'question' => 'Test',
            'order' => 1,
            'is_active' => true,
            'application_id' => $application->id,
        ];

        $this->actingAs($user)
            ->postJson(route('application-question.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('application_questions', $data);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $applicationQuestion = ApplicationQuestion::factory()->create();
        $data = [
            'question' => 'Test',
        ];
        $this->assertUpdatePermissions('application-question.update', 'applicationQuestion.update', $applicationQuestion, $data, ApplicationQuestion::class);
    });

    test('can update application question', function () {
        $user = User::factory()->owner()->create();
        $applicationQuestion = ApplicationQuestion::factory()->create();
        $application = Application::factory()->create();
        $data = [
            'question' => 'Test',
            'order' => 1,
            'is_active' => true,
            'application_id' => $application->id,
        ];

        $this->actingAs($user)
            ->patchJson(route('application-question.update', $applicationQuestion), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('application_questions', $data);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $applicationQuestion = ApplicationQuestion::factory()->create();
        $this->assertDeletePermissions('application-question.destroy', 'applicationQuestion.delete', $applicationQuestion, ApplicationQuestion::class, true);
    });

    test('can delete application question', function () {
        $user = User::factory()->owner()->create();
        $applicationQuestion = ApplicationQuestion::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('application-question.destroy', $applicationQuestion))
            ->assertOk();

        $this->assertSoftDeleted('application_questions', ['id' => $applicationQuestion->id]);
    });
});
