<?php

use App\Data\Discord\MemberData;
use App\Data\Discord\UserData;
use App\Enums\ApplicationSubmissionState;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\ApplicationSubmission;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*' => Http::response(new MemberData(
            avatar: null,
            banner: null,
            communication_disabled_until: null,
            flags: 1,
            joined_at: now()->toDateTimeString(),
            nick: '',
            pending: false,
            premium_since: '',
            roles: [],
            unusual_dm_activity_until: '',
            user: new UserData(
                id: 123,
                username: 'test',
                avatar: null,
                global_name: 'test',
                discriminator: '',
                public_flags: 1,
                flags: 1,
                accent_color: 1,
                banner: null,
                clan: null,
                primary_guild: null,
            ),
            mute: false,
            deaf: false,
        )->toArray()),
    ]);
    Http::fake([
        config('services.discord.api_url').'/users/@me/channels' => Http::response([
            'id' => '123',
        ]),
        config('services.discord.api_url').'/channels/*' => Http::response([
            'id' => '123',
            'channel_id' => '123',
        ]),
    ]);
});

describe('read operations', function () {
    test('read permission', function () {
        ApplicationSubmission::factory()->create();
        $this->assertReadPermissions('application-submission.index', 'applicationSubmission.read');
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
});

describe('create operations', function () {
    test('create permission', function () {
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
        $this->assertCreatePermissions('application-submission.store', 'applicationSubmission.create', $data, ApplicationSubmission::class);
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
});

describe('update operations', function () {
    test('update permission', function () {
        $applicationSubmission = ApplicationSubmission::factory()->create();
        $data = [
            'discord_id' => '123123123123123123',
        ];
        $this->assertUpdatePermissions('application-submission.update', 'applicationSubmission.update', $applicationSubmission, $data, ApplicationSubmission::class);
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
});

describe('delete operations', function () {
    test('delete permission', function () {
        $applicationSubmission = ApplicationSubmission::factory()->create();
        $this->assertDeletePermissions('application-submission.destroy', 'applicationSubmission.delete', $applicationSubmission, ApplicationSubmission::class);
    });

    test('can delete application submission', function () {
        $user = User::factory()->owner()->create();
        $applicationSubmission = ApplicationSubmission::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('application-submission.destroy', $applicationSubmission))
            ->assertOk();
        $this->assertDatabaseMissing('application_submissions', collect($applicationSubmission)->except('application')->toArray());
    });
});
