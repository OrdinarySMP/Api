<?php

use App\Enums\ApplicationState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\User;

test('auth user can get application', function () {
    $application = Application::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application.index'))
        ->assertOk()
        ->assertJson(['data' => [$application->toArray()]]);
});

test('can create application', function () {
    $user = User::factory()->owner()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationState::Pending->value,
        'custom_response' => 'Test',
        'message_link' => 'https://example.com',
        'handled_by' => '123123123123123123',
    ];

    $this->actingAs($user)
        ->postJson(route('application.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('applications', $data);
});

test('can update application', function () {
    $user = User::factory()->owner()->create();
    $application = Application::factory()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationState::Pending->value,
        'custom_response' => 'Test',
        'message_link' => 'https://example.com',
        'handled_by' => '123123123123123123',
    ];

    $this->actingAs($user)
        ->patchJson(route('application.update', $application), $data)
        ->assertOk()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('applications', $data);
});

test('can delete application', function () {
    $user = User::factory()->owner()->create();
    $application = Application::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application.destroy', $application))
        ->assertOk();

    $this->assertDatabaseMissing('applications', $application->toArray());
});
