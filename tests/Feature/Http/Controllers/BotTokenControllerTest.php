<?php

use App\Models\User;

use function PHPUnit\Framework\assertFalse;

test('can read bot token', function () {
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

test('can not read without permission', function () {
    $user = User::factory()->create();
    assertFalse($user->can('botToken.read'));

    $this->assertDatabaseMissing('users', ['name' => 'Discord Bot']);

    $this->actingAs($user)
        ->get(route('bot.token'))
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['name' => 'Discord Bot']);
});
