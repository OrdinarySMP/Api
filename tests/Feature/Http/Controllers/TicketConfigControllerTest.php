<?php

use App\Models\TicketConfig;
use App\Models\User;

test('auth user can get ticket configs', function () {
    $ticketConfig = TicketConfig::factory()->create();
    config(['services.discord.server_id' => $ticketConfig->guild_id]);
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('config.index'))
        ->assertOk()
        ->assertJsonPath('data.id', $ticketConfig->id);
});

test('can create ticket config', function () {
    $user = User::factory()->owner()->create();
    config(['services.discord.server_id' => '100000000000000000']);
    $data = [
        'guild_id' => '100000000000000000',
        'category_id' => '100000000000000001',
        'transcript_channel_id' => '100000000000000002',
    ];

    $this->actingAs($user)
        ->postJson(route('config.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_configs', $data);
});

test('can update ticket config', function () {
    $user = User::factory()->owner()->create();
    $ticketConfig = TicketConfig::factory()->create();
    config(['services.discord.server_id' => $ticketConfig->guild_id]);
    $data = [
        'guild_id' => $ticketConfig->guild_id,
        'category_id' => '100000000000000001',
        'transcript_channel_id' => '100000000000000002',
    ];

    $this->actingAs($user)
        ->postJson(route('config.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_configs', $data);
});
