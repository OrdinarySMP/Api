<?php

use App\Models\User;

test('auth user can get bot token', function () {
    $user = User::factory()->owner()->create();

    $this->assertDatabaseMissing('users', ['name' => 'Discord Bot']);

    $this->actingAs($user)
        ->get(route('bot.token'))
        ->assertOk()
        ->assertJsonStructure([
            'token',
        ]);

    $this->assertDatabaseHas('users', ['name' => 'Discord Bot']);

    $botUser = User::where(['name' => 'Discord Bot'])->first();
    $this->assertEquals(1, $botUser->tokens()->count());

    $this->actingAs($user)
        ->get(route('bot.token'))
        ->assertOk()
        ->assertJsonStructure([
            'token',
        ]);
    $this->assertEquals(1, $botUser->tokens()->count());
});

test('none owner user can not get bot token', function () {
    $user = User::factory()->create();

    $this->assertDatabaseMissing('users', ['name' => 'Discord Bot']);

    $this->actingAs($user)
        ->get(route('bot.token'))
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['name' => 'Discord Bot']);
});
