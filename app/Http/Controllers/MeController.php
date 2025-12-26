<?php

namespace App\Http\Controllers;

use App\Data\UserData;
use App\Repositories\DiscordRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class MeController extends Controller
{
    public function __construct(
        protected DiscordRepository $discordRepository
    ) {}

    /**
     * Get the current user.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(null, 204);
        }

        $discordGuildUser = $this->discordRepository->currentUser();

        $guild = $this->discordRepository->guild();

        if (! isset($discordGuildUser['roles'])) {
            Cache::forget('user-'.$user->id);
            Auth::guard('web')->logout();

            abort(403, 'You oauth2 token expired. Please login with Discord');
        }

        // if (! in_array(config('services.discord.required_role'), $discordGuildUser['roles'])) {
        //     Cache::forget('user-'.$user->id);
        //     Auth::guard('web')->logout();

        //     abort(403, 'You do not have the required permissions.');
        // }

        $roles = Role::whereIn('name', $discordGuildUser['roles'])->get()->pluck('name');

        if ($guild['owner_id'] === $user->discord_id) {
            $roles->push('Owner');
        }

        $user->syncRoles($roles);

        return response()->json(UserData::from($user, $guild['owner_id'] === $user->discord_id));
    }
}
