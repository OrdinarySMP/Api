<?php

use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\ApplicationSubmission;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*' => Http::response([
            'user' => [
                'id' => 123,
                'username' => 'test',
                'global_name' => 'test',
                'avatar' => null,
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
        ->assertJson(['data' => [collect($applicationSubmission)->except('application')->toArray()]]);
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
        ->assertJson(['data' => [
            ...collect($data)->except('submitted_at')->toArray(),
            'submitted_at' => Carbon::parse('2024-12-24 12:00:00')->format('Y-m-d\TH:i:s.u\Z'),
        ]]);

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
        ->assertJson(['data' => [
            ...collect($data)->except('submitted_at')->toArray(),
            'submitted_at' => Carbon::parse('2024-12-24 12:00:00')->format('Y-m-d\TH:i:s.u\Z'),
        ]]);

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
