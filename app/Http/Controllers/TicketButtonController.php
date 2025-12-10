<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateTicketButtonRequest;
use App\Data\Requests\DeleteTicketButtonRequest;
use App\Data\Requests\ReadTicketButtonRequest;
use App\Data\Requests\UpdateTicketButtonRequest;
use App\Data\TicketButtonData;
use App\Models\TicketButton;
use Spatie\LaravelData\Optional;
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
    public function index(ReadTicketButtonRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateTicketButtonRequest $request): TicketButtonData
    {
        $ticketButton = new TicketButton;
        $ticketButton->ticket_team_id = $request->ticket_team_id;
        $ticketButton->ticket_panel_id = $request->ticket_panel_id;
        $ticketButton->text = $request->text;
        $ticketButton->color = $request->color;
        $ticketButton->initial_message = $request->initial_message;
        $ticketButton->emoji = $request->emoji;
        $ticketButton->naming_scheme = $request->naming_scheme;
        $ticketButton->disabled = $request->disabled;
        $ticketButton->save();

        $ticketButtonPingRoles = collect($request->ticket_button_ping_role_ids)
            ->map(fn ($ticketButtonPingRoleId) => [
                'role_id' => $ticketButtonPingRoleId,
            ]);
        if ($ticketButtonPingRoles->isNotEmpty()) {
            $ticketButton->ticketButtonPingRoles()->createMany($ticketButtonPingRoles);
        }

        return TicketButtonData::from($ticketButton)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketButtonRequest $request, TicketButton $button): TicketButtonData
    {
        if (! $request->ticket_team_id instanceof Optional) {
            $button->ticket_team_id = $request->ticket_team_id;
        }

        if (! $request->ticket_panel_id instanceof Optional) {
            $button->ticket_panel_id = $request->ticket_panel_id;
        }

        if (! $request->text instanceof Optional) {
            $button->text = $request->text;
        }

        if (! $request->color instanceof Optional) {
            $button->color = $request->color;
        }

        if (! $request->initial_message instanceof Optional) {
            $button->initial_message = $request->initial_message;
        }

        if (! $request->emoji instanceof Optional) {
            $button->emoji = $request->emoji;
        }

        if (! $request->naming_scheme instanceof Optional) {
            $button->naming_scheme = $request->naming_scheme;
        }

        if (! $request->disabled instanceof Optional) {
            $button->disabled = $request->disabled;
        }

        if ($button->isDirty()) {
            $button->save();
        }

        if (! $request->ticket_button_ping_role_ids instanceof Optional && $request->ticket_button_ping_role_ids !== null) {
            $button->ticketButtonPingRoles()->delete();
            $ticketButtonPingRoles = collect($request->ticket_button_ping_role_ids)
                ->map(fn ($ticketButtonPingRoleId) => [
                    'role_id' => $ticketButtonPingRoleId,
                ]);
            $button->ticketButtonPingRoles()->createMany($ticketButtonPingRoles);
        }

        return TicketButtonData::from($button)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteTicketButtonRequest $request, TicketButton $button): bool
    {
        return $button->delete() ?? false;
    }
}
