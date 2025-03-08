<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationSubmission;
use App\Models\ApplicationResponse;
use App\Models\User;

test('auth user can get application submission', function () {
    $applicationSubmission = ApplicationSubmission::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application-submission.index'))
        ->assertOk()
        ->assertJson(['data' => [$applicationSubmission->toArray()]]);
});

test('can create application submission', function () {
    $user = User::factory()->owner()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationSubmissionState::Pending->value,
        'custom_response' => 'Test',
        'message_link' => 'https://example.com',
        'handled_by' => '123123123123123123',
    ];

    $this->actingAs($user)
        ->postJson(route('application-submission.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('application_submissions', $data);
});


test('can update application submission', function () {
    $user = User::factory()->owner()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationSubmissionState::Pending->value,
        'custom_response' => 'Test',
        'message_link' => 'https://example.com',
        'handled_by' => '123123123123123123',
    ];

    $this->actingAs($user)
        ->patchJson(route('application-submission.update', $applicationSubmission), $data)
        ->assertOk()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('application_submissions', $data);
});

test('can delete application', function () {
    $user = User::factory()->owner()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application-submission.destroy', $applicationSubmission))
        ->assertOk();

    $this->assertDatabaseMissing('application_submissions', $applicationSubmission->toArray());
});
