<?php

namespace App\Http\Resources;

use App\Repositories\DiscordRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketButtonPingRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>|\Illuminate\Contracts\Support\Arrayable<int, mixed>|\JsonSerializable
     */
    public function toArray(Request $request): array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    {
        $discordRepository = new DiscordRepository;

        /** @var \App\Models\TicketTeamRole $ticketButtonPingRole */
        $ticketButtonPingRole = $this->resource;

        $role = $discordRepository->roles()->first(fn ($role) => $role['id'] === $ticketButtonPingRole->role_id);

        return [
            ...$ticketButtonPingRole->toArray(),
            'role_name' => $role ? $role['name'] : 'role-not-found',
        ];
    }
}
