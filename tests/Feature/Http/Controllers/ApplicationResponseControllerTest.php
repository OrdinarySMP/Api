<?php

use App\Enums\ApplicationResponseType;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        ApplicationResponse::factory()->create();
        $this->assertReadPermissions('application-response.index', 'applicationResponse.read');
    });

    test('can read application response', function () {
        $application = ApplicationResponse::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('application-response.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $application->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $application = Application::factory()->create();
        $data = [
            'type' => ApplicationResponseType::Accepted->value,
            'name' => 'Test',
            'response' => 'Test',
            'application_id' => $application->id,
        ];
        $this->assertCreatePermissions('application-response.store', 'applicationResponse.create', $data, ApplicationResponse::class);
    });

    test('can create application response', function () {
        $user = User::factory()->owner()->create();
        $application = Application::factory()->create();
        $data = [
            'type' => ApplicationResponseType::Accepted->value,
            'name' => 'Test',
            'response' => 'Test',
            'application_id' => $application->id,
        ];

        $this->actingAs($user)
            ->postJson(route('application-response.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('application_responses', $data);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $applicationResponse = ApplicationResponse::factory()->create();
        $data = [
            'name' => 'Test',
        ];
        $this->assertUpdatePermissions('application-response.update', 'applicationResponse.update', $applicationResponse, $data, ApplicationResponse::class);
    });

    test('can update application response', function () {
        $user = User::factory()->owner()->create();
        $applicationResponse = ApplicationResponse::factory()->create();
        $application = Application::factory()->create();
        $data = [
            'type' => ApplicationResponseType::Accepted->value,
            'name' => 'Test',
            'response' => 'Test',
            'application_id' => $application->id,
        ];

        $this->actingAs($user)
            ->patchJson(route('application-response.update', $applicationResponse), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('application_responses', $data);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $applicationResponse = ApplicationResponse::factory()->create();
        $this->assertDeletePermissions('application-response.destroy', 'applicationResponse.delete', $applicationResponse, ApplicationResponse::class, true);
    });

    test('can delete application response', function () {
        $user = User::factory()->owner()->create();
        $applicationResponse = ApplicationResponse::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('application-response.destroy', $applicationResponse))
            ->assertOk();

        $this->assertSoftDeleted('application_responses', ['id' => $applicationResponse->id]);
    });
});
