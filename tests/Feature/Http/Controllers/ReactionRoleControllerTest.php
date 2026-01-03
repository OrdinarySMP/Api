<?php

use App\Models\ReactionRole;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    config(['services.discord.server_id' => '123']);
    Http::fake([
        config('services.discord.api_url').'/guilds/*/roles/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/roles' => Http::response([]),
        config('services.discord.api_url').'/channels/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/emojis' => Http::response([['id' => '123']]),
    ]);
});

describe('read operations', function () {
    test('read permission', function () {
        ReactionRole::factory()->create();
        $this->assertReadPermissions('reaction-role.index', 'reactionRole.read');
    });

    test('auth user can get reaction roles', function () {
        $reactionRole = ReactionRole::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('reaction-role.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $reactionRole->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'message_link' => 'https://discord.com/channels/123/456/789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ];
        $assertData = [
            'channel_id' => '456',
            'message_id' => '789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ];
        $this->assertCreatePermissions('reaction-role.store', 'reactionRole.create', $data, ReactionRole::class, $assertData);
    });

    test('can create rule', function () {
        $user = User::factory()->owner()->create();
        $data = [
            'message_link' => 'https://discord.com/channels/123/456/789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ];

        $this->actingAs($user)
            ->postJson(route('reaction-role.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('reaction_roles', [
            'channel_id' => '456',
            'message_id' => '789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ]);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $reactionRole = ReactionRole::factory()->create();
        $data = [
            'role_id' => '1234',
        ];
        $this->assertUpdatePermissions('reaction-role.update', 'reactionRole.update', $reactionRole, $data, ReactionRole::class);
    });

    test('can update rule', function () {
        $reactionRole = ReactionRole::factory()->create();
        $user = User::factory()->owner()->create();
        $data = [
            'message_link' => 'https://discord.com/channels/123/456/789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ];

        $this->actingAs($user)
            ->patchJson(route('reaction-role.update', $reactionRole), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('reaction_roles', [
            'channel_id' => '456',
            'message_id' => '789',
            'emoji' => '<emoji:123>',
            'role_id' => '1234',
        ]);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $reactionRole = ReactionRole::factory()->create();
        $this->assertDeletePermissions('reaction-role.destroy', 'reactionRole.delete', $reactionRole, ReactionRole::class);
    });
    test('can delete rule', function () {
        $user = User::factory()->owner()->create();
        $reactionRole = ReactionRole::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('reaction-role.destroy', $reactionRole))
            ->assertOk();

        $this->assertDatabaseMissing('reaction_roles', $reactionRole->toArray());
    });
});
