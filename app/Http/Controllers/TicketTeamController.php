<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateTicketTeamRequest;
use App\Data\Requests\DeleteTicketTeamRequest;
use App\Data\Requests\ReadTicketTeamRequest;
use App\Data\Requests\UpdateTicketTeamRequest;
use App\Data\TicketTeamData;
use App\Models\TicketTeam;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, TicketTeamData>|DataCollection<array-key, TicketTeamData>
     */
    public function index(ReadTicketTeamRequest $request): PaginatedDataCollection|DataCollection
    {
        $ticketTeams = QueryBuilder::for(TicketTeam::class)
            ->allowedIncludes(['ticketTeamRoles'])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('ticketButtons.id'),
                'name',
            ])
            ->getOrPaginate();

        if (request()->has('full')) {
            return TicketTeamData::collect($ticketTeams, DataCollection::class)->wrap('data');
        }

        return TicketTeamData::collect($ticketTeams, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketTeamRequest $request): TicketTeamData
    {
        $ticketTeam = new TicketTeam;
        $ticketTeam->name = $request->name;
        $ticketTeam->save();

        $ticketTeamRoleIds = collect($request->ticket_team_role_ids)
            ->map(fn ($ticketTeamRoleId) => [
                'role_id' => $ticketTeamRoleId,
            ]);
        if ($ticketTeamRoleIds->isNotEmpty()) {
            $ticketTeam->ticketTeamRoles()->createMany($ticketTeamRoleIds);
        }

        return TicketTeamData::from($ticketTeam)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketTeamRequest $request, TicketTeam $team): TicketTeamData
    {
        if (! $request->name instanceof Optional) {
            $team->name = $request->name;
        }

        if ($team->isDirty()) {
            $team->save();
        }

        if (! $request->ticket_team_role_ids instanceof Optional && $request->ticket_team_role_ids !== null) {
            $team->ticketTeamRoles()->delete();
            $ticketTeamRoleIds = collect($request->ticket_team_role_ids)
                ->map(fn ($ticketTeamRoleId) => [
                    'role_id' => $ticketTeamRoleId,
                ]);
            $team->ticketTeamRoles()->createMany($ticketTeamRoleIds);
        }

        return TicketTeamData::from($team)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteTicketTeamRequest $request, TicketTeam $team): bool
    {
        if (! request()->user()?->can('ticketTeam.delete')) {
            abort(403);
        }

        $team->ticketTeamRoles()->delete();

        return $team->delete() ?? false;
    }
}
