<?php

use App\Enums\DiscordButton;
use App\Models\TicketButton;
use App\Models\TicketPanel;
use App\Models\TicketTeam;
use App\Models\User;

test('auth user can get ticket buttons', function () {
    $ticketButton = TicketButton::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('button.index'))
        ->assertOk()
        ->assertJson(['data' => [$ticketButton->toArray()]]);
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
        'ticket_button_ping_role_ids' => ['123', '456'],
    ];

    $this->actingAs($user)
        ->postJson(route('button.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => collect($data)->except('ticket_button_ping_role_ids')->toArray()]);

    $this->assertDatabaseHas('ticket_buttons', collect($data)->except('ticket_button_ping_role_ids')->toArray());

    expect(TicketButton::count())->toBe(1);
    dump(TicketButton::first()->ticketButtonPingRoles);
    expect(TicketButton::first()->ticketButtonPingRoles->map(fn ($ticketButtonPingRole) => $ticketButtonPingRole->role_id)->toArray())->toBe(['123', '456']);
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
        'ticket_button_ping_role_ids' => ['123', '456'],
    ];

    $this->actingAs($user)
        ->patchJson(route('button.update', $ticketButton), $data)
        ->assertOk()
        ->assertJson(['data' => collect($data)->except('ticket_button_ping_role_ids')->toArray()]);

    $this->assertDatabaseHas('ticket_buttons', collect($data)->except('ticket_button_ping_role_ids')->toArray());
    expect($ticketButton->ticketButtonPingRoles->map(fn ($ticketButtonPingRole) => $ticketButtonPingRole->role_id)->toArray())->toBe(['123', '456']);
});

test('can delete ticket buttons', function () {
    $user = User::factory()->owner()->create();
    $ticketButton = TicketButton::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('button.destroy', $ticketButton))
        ->assertOk();

    $this->assertDatabaseMissing('ticket_buttons', $ticketButton->toArray());
});
