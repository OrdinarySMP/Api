<?php

namespace App\Repositories;

use App\Data\Discord\GuildData;
use App\Data\Discord\MemberData;
use App\Data\Discord\RoleData;
use App\Data\Discord\UserData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordRepository
{
    /**
     * @return ?Collection<int, RoleData>
     */
    public function roles(): ?Collection
    {
        $roles = Cache::remember('discord-'.config('services.discord.server_id').'-roles', 300, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/roles');

            return $response->json();
        });
        try {
            $roleData = RoleData::collect($roles, Collection::class);
        } catch (\Exception $e) {
            Log::error('Could not parse user into RoleData:', [
                'error' => $e,
                'roles' => $roles,
            ]);
            $roleData = null;
        }

        return $roleData;
    }

    public function everyoneRole(): ?RoleData
    {
        $everyoneRole = $this->roles()?->firstWhere('name', '@everyone');
        if (! $everyoneRole) {
            return null;
        }

        return Cache::remember(
            'discord-'.config('services.discord.server_id').'-role-everyone', 60 * 60 * 24 * 7, // save for 7 days
            fn () => $everyoneRole
        );
    }

    /**
     * @return Collection<int, array<mixed>>
     */
    public function channels(): Collection
    {
        /**
         * @var array<array<mixed>>
         */
        $channels = Cache::remember('discord-'.config('services.discord.server_id').'-channels', 300, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/channels');

            return $response->json();
        });

        return collect($channels);
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

    public function guild(): ?GuildData
    {
        /**
         * @var array<array<mixed>>
         */
        $guild = Cache::remember('guild-'.config('services.discord.server_id'), 600, function () {
            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id'));

            return $response->json();
        });

        try {
            $guildData = GuildData::from($guild);
        } catch (\Exception $e) {
            Log::error('Could not parse guild into GuildData:', [
                'error' => $e,
                'guild' => $guild,
            ]);
            $guildData = null;
        }

        return $guildData;
    }

    public function currentUser(): ?MemberData
    {
        /**
         * @var array<array<mixed>>
         */
        $currentUser = Cache::remember('user-'.auth()->user()?->id, 120, function () {
            $response = Http::discord()->get('/users/@me/guilds/'.config('services.discord.server_id').'/member');

            return $response->json();
        });

        try {
            $currentUserData = MemberData::from($currentUser);
        } catch (\Exception $e) {
            Log::error('Could not parse currentUser into MemberData:', [
                'error' => $e,
                'currentUser' => $currentUser,
            ]);
            $currentUserData = null;
        }

        return $currentUserData;
    }

    public function getUserById(string $userId): ?UserData
    {
        /**
         * @var array<mixed>
         */
        $user = Cache::remember('discord-user-'.$userId, 60 * 60 * 24 * 7, function () use ($userId) { // save for 7 days
            $response = Http::discordBot()->get('/users/'.$userId);

            return $response->json();
        });
        try {
            $userData = UserData::from($user);
        } catch (\Exception $e) {
            Log::error('Could not parse user into UserData:', [
                'error' => $e,
                'user' => $user,
            ]);
            $userData = null;
        }

        return $userData;
    }

    public function getGuildMemberById(string $userId): ?MemberData
    {
        $guildId = config('services.discord.server_id');
        /**
         * @var array<mixed>
         */
        $member = Cache::remember('discord-guild-'.$guildId.'-member-'.$userId, 60 * 60 * 24, function () use ($userId, $guildId) { // save for 1 days
            $response = Http::discordBot()->get("/guilds/{$guildId}/members/{$userId}");

            return $response->json();
        });

        try {
            $memberData = MemberData::from($member);
        } catch (\Exception $e) {
            Log::error('Could not parse member into MemberData:', [
                'error' => $e,
                'member' => $member,
            ]);
            $memberData = null;
        }

        return $memberData;
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
