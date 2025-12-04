<?php

use App\Models\TicketTeam;
use App\Models\User;

test('auth user can get ticket teams', function () {
    $ticketTeam = TicketTeam::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('team.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ticketTeam->id);
});

test('can create ticket team', function () {
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->postJson(route('team.store'), [
            'name' => 'Test',
            'ticket_team_role_ids' => ['123', '456'],
        ])
        ->assertCreated()
        ->assertJson(['data' => ['name' => 'Test']]);

    $this->assertDatabaseHas('ticket_teams', ['name' => 'Test']);
    expect(TicketTeam::count())->toBe(1);
    expect(TicketTeam::first()->ticketTeamRoles->map(fn ($teamRole) => $teamRole->role_id)->toArray())->toBe(['123', '456']);
});

test('can update ticket team', function () {
    $user = User::factory()->owner()->create();
    $ticketTeam = TicketTeam::factory()->create();

    $this->actingAs($user)
        ->patchJson(route('team.update', $ticketTeam), [
            'name' => 'Test',
            'ticket_team_role_ids' => ['123', '456'],
        ])
        ->assertOk()
        ->assertJson(['data' => ['name' => 'Test']]);

    $this->assertDatabaseHas('ticket_teams', ['name' => 'Test']);
    expect($ticketTeam->ticketTeamRoles->map(fn ($teamRole) => $teamRole->role_id)->toArray())->toBe(['123', '456']);
});

test('can delete ticket team', function () {
    $user = User::factory()->owner()->create();
    $ticketTeam = TicketTeam::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('team.destroy', $ticketTeam))
        ->assertOk();

    $this->assertDatabaseMissing('ticket_teams', $ticketTeam->toArray());
});
