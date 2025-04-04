<?php

use App\Models\ServerContent;
use App\Models\User;

test('auth user can get server contents', function () {
    $serverContent = ServerContent::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('server-content.index'))
        ->assertOk()
        ->assertJson(['data' => [$serverContent->toArray()]]);
});

test('can create server content', function () {
    $user = User::factory()->owner()->create();
    $data = [
        'name' => 'Test',
        'url' => 'https://example.com',
        'description' => 'Test Content',
        'is_recommended' => true,
        'is_active' => true,
    ];

    $this->actingAs($user)
        ->postJson(route('server-content.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('server_contents', $data);
});

test('can update server content', function () {
    $user = User::factory()->owner()->create();
    $serverContent = ServerContent::factory()->create();
    $data = [
        'name' => 'Test',
        'url' => 'https://example.com',
        'description' => 'Test Content',
        'is_recommended' => true,
        'is_active' => true,
    ];

    $this->actingAs($user)
        ->patchJson(route('server-content.update', $serverContent), $data)
        ->assertOk()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('server_contents', $data);
});

test('can delete server content', function () {
    $user = User::factory()->owner()->create();
    $serverContent = ServerContent::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('server-content.destroy', $serverContent))
        ->assertOk();

    $this->assertDatabaseMissing('server_contents', $serverContent->toArray());
});
