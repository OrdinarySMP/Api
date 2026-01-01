<?php

use App\Enums\DiscordButton;
use App\Models\TicketButton;
use App\Models\TicketPanel;
use App\Models\TicketTeam;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        TicketButton::factory()->create();
        $this->assertReadPermissions('button.index', 'ticketButton.read');
    });

    test('can read ticket buttons', function () {
        $ticketButton = TicketButton::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('button.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticketButton->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $panel = TicketPanel::factory()->create();
        $team = TicketTeam::factory()->create();
        $data = [
            'ticket_team_id' => $team->id,
            'ticket_panel_id' => $panel->id,
            'text' => 'Test',
            'color' => DiscordButton::Success->value,
            'initial_message' => 'Test',
            'emoji' => '⚠️',
            'naming_scheme' => '%id%-Channel',
            'disabled' => false,
            'ticket_button_ping_role_ids' => ['123', '456'],
        ];
        $this->assertCreatePermissions('button.store', 'ticketButton.create', $data, TicketButton::class, collect($data)->except('ticket_button_ping_role_ids')->toArray());
    });

    test('can create ticket buttons', function () {
        $user = User::factory()->owner()->create();
        $panel = TicketPanel::factory()->create();
        $team = TicketTeam::factory()->create();
        $data = [
            'ticket_team_id' => $team->id,
            'ticket_panel_id' => $panel->id,
            'text' => 'Test',
            'color' => DiscordButton::Success->value,
            'initial_message' => 'Test',
            'emoji' => '⚠️',
            'naming_scheme' => '%id%-Channel',
            'disabled' => false,
            'ticket_button_ping_role_ids' => ['123', '456'],
        ];

        $this->actingAs($user)
            ->postJson(route('button.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => collect($data)->except('ticket_button_ping_role_ids')->toArray()]);

        $this->assertDatabaseHas('ticket_buttons', collect($data)->except('ticket_button_ping_role_ids')->toArray());

        expect(TicketButton::count())->toBe(1);
        expect(TicketButton::first()->ticketButtonPingRoles->map(fn ($ticketButtonPingRole) => $ticketButtonPingRole->role_id)->toArray())->toBe(['123', '456']);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $ticketButton = TicketButton::factory()->create();
        $data = [
            'text' => 'Test',
        ];
        $this->assertUpdatePermissions('button.update', 'ticketButton.update', $ticketButton, $data, TicketButton::class);
    });

    test('can update ticket buttons', function () {
        $user = User::factory()->owner()->create();
        $ticketButton = TicketButton::factory()->create();
        $panel = TicketPanel::factory()->create();
        $team = TicketTeam::factory()->create();
        $data = [
            'ticket_team_id' => $team->id,
            'ticket_panel_id' => $panel->id,
            'text' => 'Test',
            'color' => DiscordButton::Success->value,
            'initial_message' => 'Test',
            'emoji' => '⚠️',
            'naming_scheme' => '%id%-Channel',
            'disabled' => false,
            'ticket_button_ping_role_ids' => ['123', '456'],
        ];

        $this->actingAs($user)
            ->patchJson(route('button.update', $ticketButton), $data)
            ->assertOk()
            ->assertJson(['data' => collect($data)->except('ticket_button_ping_role_ids')->toArray()]);

        $this->assertDatabaseHas('ticket_buttons', collect($data)->except('ticket_button_ping_role_ids')->toArray());
        expect($ticketButton->ticketButtonPingRoles->map(fn ($ticketButtonPingRole) => $ticketButtonPingRole->role_id)->toArray())->toBe(['123', '456']);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $ticketButton = TicketButton::factory()->create();
        $this->assertDeletePermissions('button.destroy', 'ticketButton.delete', $ticketButton, TicketButton::class);
    });

    test('can delete ticket buttons', function () {
        $user = User::factory()->owner()->create();
        $ticketButton = TicketButton::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('button.destroy', $ticketButton))
            ->assertOk();

        $this->assertDatabaseMissing('ticket_buttons', $ticketButton->toArray());
    });
});
