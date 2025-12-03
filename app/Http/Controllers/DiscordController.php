<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\DiscordRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DiscordController extends Controller
{
    public function __construct(
        protected DiscordRepository $discordRepository
    ) {}

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
            'nickname' => $user->user['global_name'],
            'avatar' => $user->avatar,
            'discord_token' => $user->token,
            'discord_refresh_token' => $user->refreshToken,
        ]);

        Auth::guard('web')->login($user, true);

        $request->session()->regenerate();

        return response()->json([], 204);
    }

    public function textChannels(): JsonResponse
    {
        return response()->json(
            $this->discordRepository->textChannels()->values()->toArray(),
            200
        );
    }

    public function categories(): JsonResponse
    {
        return response()->json(
            $this->discordRepository->categories()->values()->toArray(),
            200
        );
    }

    public function roles(): JsonResponse
    {
        return response()->json(
            $this->discordRepository->roles(),
            200
        );
    }
}
