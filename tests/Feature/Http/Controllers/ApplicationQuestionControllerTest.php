<?php

use App\Models\Application;
use App\Models\ApplicationQuestion;
use App\Models\User;

test('auth user can get application question', function () {
    $applicationQuestion = ApplicationQuestion::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application-question.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $applicationQuestion->id);

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

test('can delete application question', function () {
    $user = User::factory()->owner()->create();
    $applicationQuestion = ApplicationQuestion::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application-question.destroy', $applicationQuestion))
        ->assertOk();

    $this->assertSoftDeleted('application_questions', ['id' => $applicationQuestion->id]);
});
