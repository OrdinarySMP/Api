<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DiscordController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback(Request $request): JsonResponse
    {
        /** @phpstan-ignore method.notFound */
        $user = Socialite::driver('discord')->stateless()->user();

        $response = Http::discord($user->token)->get('/users/@me/guilds/'.config('services.discord.server_id').'/member');
        $json = $response->json();

        if (! $json || ! isset($json['roles'])) {
            abort(404);
        }

        if (! in_array(config('services.discord.required_role'), $json['roles'])) {
            abort(404);
        }

        $user = User::updateOrCreate([
            'discord_id' => $user->id,
        ], [
            'name' => $user->name,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'discord_token' => $user->token,
            'discord_refresh_token' => $user->refreshToken,
        ]);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return response()->json([], 204);
    }
}
