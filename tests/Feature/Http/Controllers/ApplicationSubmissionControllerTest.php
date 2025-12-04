<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\ApplicationSubmission;
use App\Models\User;

beforeEach(function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*' => Http::response([
            'flags' => 1,
            'nick' => '',
            'pending' => false,
            'premium_since' => '',
            'roles' => [],
            'unusual_dm_activity_until' => '',
            'mute' => false,
            'deaf' => false,
            'user' => [
                'id' => 123,
                'username' => 'test',
                'global_name' => 'test',
                'discriminator' => '',
                'public_flags' => 1,
                'flags' => 1,
                'accent_color' => 1,
                'banner_color' => '',
            ],
            'joined_at' => now()->toDateTimeString(),
        ]),
    ]);
    Http::fake([
        config('services.discord.api_url').'/channels/*' => Http::response([
            'id' => 123,
            'channel_id' => 123,
        ]),
    ]);
});

test('auth user can get application submission', function () {
    $applicationSubmission = ApplicationSubmission::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application-submission.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $applicationSubmission->id);
});

test('can create application submission', function () {
    $user = User::factory()->owner()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $application = Application::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationSubmissionState::Pending->value,
        'custom_response' => 'Test',
        'handled_by' => '123123123123123123',
        'application_id' => $application->id,
    ];

    $this->actingAs($user)
        ->postJson(route('application-submission.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => collect($data)->except('submitted_at')->toArray()]);

    $this->assertDatabaseHas('application_submissions', $data);
});

test('can update application submission', function () {
    $user = User::factory()->owner()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create();
    $applicationResponse = ApplicationResponse::factory()->create();
    $application = Application::factory()->create();
    $data = [
        'discord_id' => '123123123123123123',
        'submitted_at' => '2024-12-24 12:00:00',
        'application_response_id' => $applicationResponse->id,
        'state' => ApplicationSubmissionState::Pending->value,
        'custom_response' => 'Test',
        'handled_by' => '123123123123123123',
        'application_id' => $application->id,
    ];

    $this->actingAs($user)
        ->patchJson(route('application-submission.update', $applicationSubmission), $data)
        ->assertOk()
        ->assertJson(['data' => collect($data)->except('submitted_at')->toArray()]);

    $this->assertDatabaseHas('application_submissions', $data);
});

test('can delete application submission', function () {
    $user = User::factory()->owner()->create();
    $applicationSubmission = ApplicationSubmission::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application-submission.destroy', $applicationSubmission))
        ->assertOk();
    $this->assertDatabaseMissing('application_submissions', collect($applicationSubmission)->except('application')->toArray());
});
