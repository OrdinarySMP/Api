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

        if (! $discordGuildUser?->roles) {
            Cache::forget('user-'.$user->id);
            Auth::guard('web')->logout();

            abort(403, 'You oauth2 token expired. Please login with Discord');
        }

        $userRoles = collect($discordGuildUser->roles);

        $everyoneRole = $this->discordRepository->everyoneRole();
        if ($everyoneRole) {
            $userRoles->push($everyoneRole->id);
        }

        $roles = Role::whereIn('name', $userRoles)->get()->pluck('name');
        $userData = UserData::from($user);

        if ($userData->is_owner) {
            $roles->push('Owner');
        }

        $user->syncRoles($roles);

        return response()->json($userData);
    }
}
