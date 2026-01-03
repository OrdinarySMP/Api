<?php

use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\TicketButton;
use App\Models\TicketConfig;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    config(['services.discord.server_id' => '100000000000000000']);
    TicketConfig::factory()->create(['guild_id' => '100000000000000000']);

    Http::fake([
        config('services.discord.api_url').'/users/*' => Http::response([]),
        config('services.discord.api_url').'/roles/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/roles' => Http::response([[
            'id' => '100000000000000000',
            'name' => '@everyone',
        ]]),
        config('services.discord.api_url').'/guilds/*/channels' => Http::response(['id' => '123'], 201),
        config('services.discord.api_url').'/channels/*' => Http::response(['id' => '456']),
        config('services.discord.api_url').'/channels/*' => Http::response([]),
    ]);
});

describe('read operations', function () {
    test('read permission', function () {
        Ticket::factory()->create();
        $this->assertReadPermissions('ticket.index', 'ticket.read');
    });
    test('can read tickets', function () {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('ticket.index'))
            ->assertOk()
            ->assertJson(['data' => [['id' => $ticket->id]]]);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $button = TicketButton::factory()->create();
        $data = [
            'ticket_button_id' => $button->id,
            'created_by_discord_user_id' => '100000000000000000',
        ];
        $this->assertCreatePermissions('ticket.store', 'ticket.create', $data, Ticket::class);
    });

    test('can create tickets', function () {
        $user = User::factory()->bot()->create();
        $button = TicketButton::factory()->create();
        $data = [
            'ticket_button_id' => $button->id,
            'created_by_discord_user_id' => '100000000000000000',
        ];

        $this->actingAs($user)
            ->postJson(route('ticket.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => [
                ...$data,
                'channel_id' => '123',
                'state' => TicketState::Open->value,
            ]]);

        $this->assertDatabaseHas('tickets', [
            ...$data,
            'channel_id' => '123',
            'state' => TicketState::Open->value,
            'closed_by_discord_user_id' => null,
            'closed_reason' => null,
        ]);
    });
});

describe('close operations', function () {
    test('can close tickets', function () {
        $user = User::factory()->owner()->create();
        $ticket = Ticket::factory()->create();

        $data = [
            'closed_by_discord_user_id' => '100000000000000001',
            'closed_reason' => 'Test',
        ];

        $this->actingAs($user)
            ->postJson(route('ticket.close', $ticket), $data)
            ->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            ...$data,
        ]);
    });
})->todo('add permission test');
