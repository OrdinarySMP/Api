<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketButtonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>|\Illuminate\Contracts\Support\Arrayable<int, mixed>|\JsonSerializable
     */
    public function toArray(Request $request): array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    {
        /** @var \App\Models\TicketButton $ticketButton */
        $ticketButton = $this->resource;

        return [
            ...$ticketButton->toArray(),
            'color_name' => $ticketButton->color->name,
            'ticket_button_ping_roles' => TicketButtonPingRoleResource::collection($this->whenLoaded('ticketButtonPingRoles')),
        ];
    }
}
