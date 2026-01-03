<?php

use App\Models\TicketTeam;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        TicketTeam::factory()->create();
        $this->assertReadPermissions('team.index', 'ticketTeam.read');
    });

    test('can read ticket teams', function () {
        $ticketTeam = TicketTeam::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('team.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticketTeam->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'name' => 'Test',
            'ticket_team_role_ids' => ['123', '456'],
        ];
        $this->assertCreatePermissions('team.store', 'ticketTeam.create', $data, TicketTeam::class, ['name' => 'Test']);
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
});

describe('update operations', function () {
    test('update permission', function () {
        $ticketTeam = TicketTeam::factory()->create();
        $data = [
            'name' => 'Test',
        ];
        $this->assertUpdatePermissions('team.update', 'ticketTeam.update', $ticketTeam, $data, TicketTeam::class);
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
});

describe('delete operations', function () {
    test('delete permission', function () {
        $ticketTeam = TicketTeam::factory()->create();
        $this->assertDeletePermissions('team.destroy', 'ticketTeam.delete', $ticketTeam, TicketTeam::class);
    });

    test('can delete ticket team', function () {
        $user = User::factory()->owner()->create();
        $ticketTeam = TicketTeam::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('team.destroy', $ticketTeam))
            ->assertOk();

        $this->assertDatabaseMissing('ticket_teams', $ticketTeam->toArray());
    });
});
