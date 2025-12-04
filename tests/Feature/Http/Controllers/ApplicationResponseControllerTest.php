<?php

use App\Enums\ApplicationResponseType;
use App\Models\Application;
use App\Models\ApplicationResponse;
use App\Models\User;

test('auth user can get application response', function () {
    $application = ApplicationResponse::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('application-response.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $application->id);

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

test('can delete application response', function () {
    $user = User::factory()->owner()->create();
    $applicationResponse = ApplicationResponse::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('application-response.destroy', $applicationResponse))
        ->assertOk();

    $this->assertSoftDeleted('application_responses', ['id' => $applicationResponse->id]);
});
