<?php

use App\Models\Application;
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
    $data = [
        'name' => 'name',
        'is_active' => true,
        'log_channel' => 'log_channel',
        'accept_message' => 'accept_message',
        'deny_message' => 'deny_message',
        'confirmation_message' => 'confirmation_message',
        'completion_message' => 'completion_message',
        'restricted_role_ids' => ['1', '2'],
        'accepted_role_ids' => ['3', '4'],
        'denied_role_ids' => ['5', '6'],
        'ping_role_ids' => ['7', '8'],
        'accept_removal_role_ids' => ['9', '10'],
        'deny_removal_role_ids' => ['11', '12'],
        'pending_role_ids' => ['13', '14'],
    ];
    $validationData = collect($data)->except('restricted_role_ids', 'accepted_role_ids', 'denied_role_ids', 'ping_role_ids', 'accept_removal_role_ids', 'deny_removal_role_ids', 'pending_role_ids')->toArray();

    $this->actingAs($user)
        ->postJson(route('application.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $validationData]);

    $this->assertDatabaseHas('applications', $validationData);

    expect(Application::count())->toBe(1);
    expect(Application::first()->restrictedRoles->map(fn ($restrictedRole) => $restrictedRole->role_id)->toArray())->toBe(['1', '2']);
    expect(Application::first()->acceptedRoles->map(fn ($acceptedRole) => $acceptedRole->role_id)->toArray())->toBe(['3', '4']);
    expect(Application::first()->deniedRoles->map(fn ($deniedRole) => $deniedRole->role_id)->toArray())->toBe(['5', '6']);
    expect(Application::first()->pingRoles->map(fn ($pingRole) => $pingRole->role_id)->toArray())->toBe(['7', '8']);
    expect(Application::first()->acceptRemovalRoles->map(fn ($acceptRemovalRole) => $acceptRemovalRole->role_id)->toArray())->toBe(['9', '10']);
    expect(Application::first()->denyRemovalRoles->map(fn ($denyRemovalRole) => $denyRemovalRole->role_id)->toArray())->toBe(['11', '12']);
    expect(Application::first()->pendingRoles->map(fn ($pendingRole) => $pendingRole->role_id)->toArray())->toBe(['13', '14']);
});

test('can update application', function () {
    $user = User::factory()->owner()->create();
    $application = Application::factory()->create();
    $data = [
        'name' => 'name',
        'is_active' => true,
        'log_channel' => 'log_channel',
        'accept_message' => 'accept_message',
        'deny_message' => 'deny_message',
        'confirmation_message' => 'confirmation_message',
        'completion_message' => 'completion_message',
        'restricted_role_ids' => ['1', '2'],
        'accepted_role_ids' => ['3', '4'],
        'denied_role_ids' => ['5', '6'],
        'ping_role_ids' => ['7', '8'],
        'accept_removal_role_ids' => ['9', '10'],
        'deny_removal_role_ids' => ['11', '12'],
        'pending_role_ids' => ['13', '14'],
    ];
    $validationData = collect($data)->except('restricted_role_ids', 'accepted_role_ids', 'denied_role_ids', 'ping_role_ids', 'accept_removal_role_ids', 'deny_removal_role_ids', 'pending_role_ids')->toArray();

    $this->actingAs($user)
        ->patchJson(route('application.update', $application), $data)
        ->assertOk()
        ->assertJson(['data' => $validationData]);

    $this->assertDatabaseHas('applications', $validationData);

    expect($application->restrictedRoles->map(fn ($restrictedRole) => $restrictedRole->role_id)->toArray())->toBe(['1', '2']);
    expect($application->acceptedRoles->map(fn ($acceptedRole) => $acceptedRole->role_id)->toArray())->toBe(['3', '4']);
    expect($application->deniedRoles->map(fn ($deniedRole) => $deniedRole->role_id)->toArray())->toBe(['5', '6']);
    expect($application->pingRoles->map(fn ($pingRole) => $pingRole->role_id)->toArray())->toBe(['7', '8']);
    expect($application->acceptRemovalRoles->map(fn ($acceptRemovalRole) => $acceptRemovalRole->role_id)->toArray())->toBe(['9', '10']);
    expect($application->denyRemovalRoles->map(fn ($denyRemovalRole) => $denyRemovalRole->role_id)->toArray())->toBe(['11', '12']);
    expect($application->pendingRoles->map(fn ($pendingRole) => $pendingRole->role_id)->toArray())->toBe(['13', '14']);
});

test('can delete application', function () {
    $user = User::factory()->owner()->create();
    $application = Application::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application.destroy', $application))
        ->assertOk();

    $this->assertSoftDeleted('applications', ['id' => $application->id]);
});
