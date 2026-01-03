<?php

use App\Models\TicketConfig;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        TicketConfig::factory()->create();
        $this->assertReadPermissions('config.index', 'ticketConfig.read');
    });

    test('can read ticket configs', function () {
        $ticketConfig = TicketConfig::factory()->create();
        config(['services.discord.server_id' => $ticketConfig->guild_id]);
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('config.index'))
            ->assertOk()
            ->assertJsonPath('data.id', $ticketConfig->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        config(['services.discord.server_id' => '100000000000000000']);
        $data = [
            'guild_id' => '100000000000000000',
            'category_id' => '100000000000000001',
            'transcript_channel_id' => '100000000000000002',
        ];
        $this->assertCreatePermissions('config.store', 'ticketConfig.create', $data, TicketConfig::class);
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
});

describe('update operations', function () {
    test('create permission', function () {
        $ticketConfig = TicketConfig::factory()->create();
        config(['services.discord.server_id' => $ticketConfig->guild_id]);
        $data = [
            'guild_id' => $ticketConfig->guild_id,
            'category_id' => '100000000000000001',
            'transcript_channel_id' => '100000000000000002',
        ];
        $this->assertCreatePermissions('config.store', 'ticketConfig.create', $data, TicketConfig::class);
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
});
