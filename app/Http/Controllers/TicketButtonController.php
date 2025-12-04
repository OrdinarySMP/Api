<?php

namespace App\Http\Controllers;

use App\Data\TicketButtonData;
use App\Http\Requests\TicketButton\StoreRequest;
use App\Http\Requests\TicketButton\UpdateRequest;
use App\Models\TicketButton;
use App\Models\TicketButtonPingRole;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketButtonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, TicketButtonData>
     */
    public function index(): PaginatedDataCollection
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

        return TicketButtonData::collect($ticketButtons, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketButtonData
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

        return TicketButtonData::from($button)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketButton $button): TicketButtonData
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

        return TicketButtonData::from($button)->wrap('data');
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
