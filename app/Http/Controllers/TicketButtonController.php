<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketButton\StoreRequest;
use App\Http\Requests\TicketButton\UpdateRequest;
use App\Http\Resources\TicketButtonResource;
use App\Models\TicketButton;
use App\Models\TicketButtonPingRole;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketButtonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('ticketButton.read')) {
            abort(403);
        }
        $ticketButtons = QueryBuilder::for(TicketButton::class)
            ->allowedIncludes(['ticketButtonPingRoles'])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('ticket_panel_id'),
                AllowedFilter::exact('ticket_team_id'),
            ])
            ->getOrPaginate();

        return TicketButtonResource::collection($ticketButtons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketButtonResource
    {
        $data = $request->validated();
        $button = TicketButton::create($data);

        if (array_key_exists('ticket_button_ping_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $ticketButtonPingRoleIds = $data['ticket_button_ping_role_ids'];
            $ticketButtonPingRoles = collect($ticketButtonPingRoleIds)->map(fn ($ticketButtonPingRoleId) => [
                'role_id' => $ticketButtonPingRoleId,
                'ticket_button_id' => $button->id,
            ]);
            TicketButtonPingRole::insert($ticketButtonPingRoles->toArray());
        }

        return new TicketButtonResource($button);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketButton $button): TicketButtonResource
    {
        $data = $request->validated();
        $button->update($data);

        if (array_key_exists('ticket_button_ping_role_ids', $data)) {
            $button->ticketButtonPingRoles()->delete();
            /**
             * @var array<string>
             */
            $ticketButtonPingRoleIds = $data['ticket_button_ping_role_ids'];
            $ticketButtonPingRoles = collect($ticketButtonPingRoleIds)->map(fn ($ticketButtonPingRoleId) => [
                'role_id' => $ticketButtonPingRoleId,
                'ticket_button_id' => $button->id,
            ]);
            TicketButtonPingRole::insert($ticketButtonPingRoles->toArray());
        }

        return new TicketButtonResource($button);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketButton $button): bool
    {
        if (! request()->user()?->can('ticketButton.delete')) {
            abort(403);
        }

        return $button->delete() ?? false;
    }
}
