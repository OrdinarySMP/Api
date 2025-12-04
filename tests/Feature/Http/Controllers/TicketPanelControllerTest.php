<?php

use App\Models\TicketPanel;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('auth user can get ticket panels', function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*/channels' => Http::response([]),
    ]);

    $ticketPanel = TicketPanel::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('panel.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ticketPanel->id);
});

test('can create ticket panel', function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*/channels' => Http::response([]),
    ]);

    $user = User::factory()->owner()->create();
    $data = [
        'title' => 'Test',
        'message' => 'Test',
        'embed_color' => '#012345',
        'channel_id' => '100000000000000000',
    ];

    $this->actingAs($user)
        ->postJson(route('panel.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_panels', $data);
});

test('can update ticket panel', function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*/channels' => Http::response([]),
    ]);

    $user = User::factory()->owner()->create();
    $ticketPanel = TicketPanel::factory()->create();
    $data = [
        'title' => 'Test',
        'message' => 'Test',
        'embed_color' => '#012345',
        'channel_id' => '100000000000000000',
    ];

    $this->actingAs($user)
        ->patchJson(route('panel.update', $ticketPanel), $data)
        ->assertOk()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_panels', $data);
});

test('can delete ticket panel', function () {
    $user = User::factory()->owner()->create();
    $ticketPanel = TicketPanel::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('panel.destroy', $ticketPanel))
        ->assertOk();

    $this->assertDatabaseMissing('ticket_panels', $ticketPanel->toArray());
});

test('can send ticket panel', function () {
    Http::fake([
        config('services.discord.api_url').'/channels/*' => Http::response([]),
    ]);

    $user = User::factory()->owner()->create();
    $ticketPanel = TicketPanel::factory()->create();

    $this->actingAs($user)
        ->postJson(route('panel.send', $ticketPanel))
        ->assertOk();
});
