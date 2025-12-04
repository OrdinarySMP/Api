<?php

use App\Models\ReactionRole;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('auth user can get reaction roles', function () {
    Http::fake([
        config('services.discord.api_url').'/guilds/*/roles' => Http::response([]),
    ]);

    $reactionRole = ReactionRole::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('reaction-role.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $reactionRole->id);
});

test('can create rule', function () {
    config(['services.discord.server_id' => '123']);
    Http::fake([
        config('services.discord.api_url').'/guilds/*/roles/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/roles' => Http::response([]),
        config('services.discord.api_url').'/channels/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/emojis' => Http::response([['id' => '123']]),
    ]);

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

test('can update rule', function () {
    config(['services.discord.server_id' => '123']);
    Http::fake([
        config('services.discord.api_url').'/guilds/*/roles/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/roles' => Http::response([]),
        config('services.discord.api_url').'/channels/*' => Http::response([]),
        config('services.discord.api_url').'/guilds/*/emojis' => Http::response([['id' => '123']]),
    ]);

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

test('can delete rule', function () {
    Http::fake([
        config('services.discord.api_url').'/channels/*' => Http::response([]),
    ]);

    $user = User::factory()->owner()->create();
    $reactionRole = ReactionRole::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('reaction-role.destroy', $reactionRole))
        ->assertOk();

    $this->assertDatabaseMissing('reaction_roles', $reactionRole->toArray());
});
