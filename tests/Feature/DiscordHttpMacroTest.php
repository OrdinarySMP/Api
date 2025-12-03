<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;

it('refreshes discord token and retries the request', function () {
    $user = User::factory()->create([
        'discord_token' => 'old_access_token',
        'discord_refresh_token' => 'old_refresh_token',
    ]);

    actingAs($user);

    Http::fake([
        // Must be first
        'https://discord.com/api/oauth2/token' => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
        ], 200),

        // Must be second
        'https://discord.com/api/v10/*' => Http::sequence()
            ->push('', 401)
            ->push(['data' => 'ok'], 200),
    ]);

    $response = Http::discord()->get('/users/@me');

    expect($response->json('data'))->toBe('ok');

    expect($user->refresh()->discord_token)->toBe('new_access_token');
    expect($user->refresh()->discord_refresh_token)->toBe('new_refresh_token');
});
