<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordRepository
{
    /**
     * @return Collection<int, array<string>>
     */
    public function roles(): Collection
    {
        /**
         * @var array<array<string>>
         */
        $roles = Cache::remember('discord-'.config('services.discord.server_id').'-roles', 300, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/roles');

            return $response->json();
        });

        return collect($roles);
    }

    /**
     * @return Collection<int, array<mixed>>
     */
    public function channels(): Collection
    {
        /**
         * @var array<array<mixed>>
         */
        $roles = Cache::remember('discord-'.config('services.discord.server_id').'-channels', 300, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/channels');

            return $response->json();
        });

        return collect($roles);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function textChannels(): Collection
    {
        $channels = $this->channels();
        $textChannels = $channels->filter(fn ($channel) => $channel['type'] === 0);

        return $textChannels;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function categories(): Collection
    {
        $channels = $this->channels();
        $textChannels = $channels->filter(fn ($channel) => $channel['type'] === 4);

        return $textChannels;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function guild(): Collection
    {
        /**
         * @var array<array<mixed>>
         */
        $guild = Cache::remember('guild-'.config('services.discord.server_id'), 600, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id'));

            return $response->json();
        });

        return collect($guild);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function currentUser(): Collection
    {
        /**
         * @var array<array<mixed>>
         */
        $currentUser = Cache::remember('user-'.auth()->user()?->id, 120, function () {
            $response = Http::discord()->get('/users/@me/guilds/'.config('services.discord.server_id').'/member');

            return $response->json();
        });

        return collect($currentUser);
    }

    /**
     * @return array<mixed>
     */
    public function getUserById(string $userId): array
    {
        /**
         * @var array<mixed>
         */
        $user = Cache::remember('discord-user-'.$userId, 60 * 60 * 24 * 7, function () use ($userId) { // save for 7 days
            $response = Http::discordBot()->get('/users/'.$userId);

            return $response->json();
        });

        return $user;
    }

    /**
     * @return array<mixed>
     */
    public function getGuildMemberById(string $userId): array
    {
        $guildId = config('services.discord.server_id');
        /**
         * @var array<mixed>
         */
        $member = Cache::remember('discord-guild-'.$guildId.'-member-'.$userId, 60 * 60 * 24, function () use ($userId, $guildId) { // save for 1 days
            $response = Http::discordBot()->get("/guilds/{$guildId}/members/{$userId}");

            return $response->json();
        });

        if (! $member || ! array_key_exists('user', $member)) {
            Log::error("Could not find member for user id: {$userId}.", $member);
            throw new \Error("Could not find member for user id: {$userId}.");
        }

        return $member;
    }

    public function addRoleToMember(string $roleId, string $userId, ?string $reason = null): bool
    {
        $guildId = config('services.discord.server_id');
        $request = Http::discordBot();
        if ($reason) {
            $request = $request->withHeaders([
                'X-Audit-Log-Reason' => $reason,
            ]);
        }
        $response = $request->put("/guilds/{$guildId}/members/{$userId}/roles/{$roleId}");

        return $response->noContent();
    }

    public function removeRoleFromMember(string $roleId, string $userId, ?string $reason = null): bool
    {
        $guildId = config('services.discord.server_id');
        $request = Http::discordBot();
        if ($reason) {
            $request = $request->withHeaders([
                'X-Audit-Log-Reason' => $reason,
            ]);
        }
        $response = $request->delete("/guilds/{$guildId}/members/{$userId}/roles/{$roleId}");

        return $response->noContent();
    }
}
