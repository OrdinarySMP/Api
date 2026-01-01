<?php

use App\Models\TicketPanel;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*/channels' => Http::response([]),
        config('services.discord.api_url').'/channels/*' => Http::response([]),
    ]);
});

describe('read operations', function () {
    test('read permission', function () {
        TicketPanel::factory()->create();
        $this->assertReadPermissions('panel.index', 'ticketPanel.read');
    });

    test('can read ticket panels', function () {
        $ticketPanel = TicketPanel::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticketPanel->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'title' => 'Test',
            'message' => 'Test',
            'embed_color' => '#012345',
            'channel_id' => '100000000000000000',
        ];
        $this->assertCreatePermissions('panel.store', 'ticketPanel.create', $data, TicketPanel::class);
    });

    test('can create ticket panel', function () {
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
});

describe('update operations', function () {
    test('update permission', function () {
        $ticketPanel = TicketPanel::factory()->create();
        $data = [
            'title' => 'Test',
        ];
        $this->assertUpdatePermissions('panel.update', 'ticketPanel.update', $ticketPanel, $data, TicketPanel::class);
    });

    test('can update ticket panel', function () {
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
});

describe('delete operations', function () {
    test('delete permission', function () {
        $ticketPanel = TicketPanel::factory()->create();
        $this->assertDeletePermissions('panel.destroy', 'ticketPanel.delete', $ticketPanel, TicketPanel::class);
    });

    test('can delete ticket panel', function () {
        $user = User::factory()->owner()->create();
        $ticketPanel = TicketPanel::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('panel.destroy', $ticketPanel))
            ->assertOk();

        $this->assertDatabaseMissing('ticket_panels', $ticketPanel->toArray());
    });
});

describe('send operations', function () {
    test('can send ticket panel', function () {
        $user = User::factory()->owner()->create();
        $ticketPanel = TicketPanel::factory()->create();

        $this->actingAs($user)
            ->postJson(route('panel.send', $ticketPanel))
            ->assertOk();
    });
})->todo('add permission check');
