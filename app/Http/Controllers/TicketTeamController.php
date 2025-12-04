<?php

namespace App\Http\Controllers;

use App\Data\TicketTeamData;
use App\Http\Requests\TicketTeam\StoreRequest;
use App\Http\Requests\TicketTeam\UpdateRequest;
use App\Models\TicketTeam;
use App\Models\TicketTeamRole;
use Spatie\LaravelData\DataCollection;
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
    public function index(): PaginatedDataCollection|DataCollection
    {
        if (! request()->user()?->can('ticketTeam.read')) {
            abort(403);
        }
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
    public function store(StoreRequest $request): TicketTeamData
    {
        $data = $request->validated();
        $team = TicketTeam::create($data);
        if (array_key_exists('ticket_team_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $ticketTeamRoleIds = $data['ticket_team_role_ids'];
            $tickeTeamRoles = collect($ticketTeamRoleIds)->map(fn ($ticket_team_role_id) => [
                'role_id' => $ticket_team_role_id,
                'ticket_team_id' => $team->id,
            ]);
            TicketTeamRole::insert($tickeTeamRoles->toArray());
        }

        return TicketTeamData::from($team)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketTeam $team): TicketTeamData
    {
        $data = $request->validated();
        $team->update($data);
        if (array_key_exists('ticket_team_role_ids', $data)) {
            $team->ticketTeamRoles()->delete();
            /**
             * @var array<string>
             */
            $ticketTeamRoleIds = $data['ticket_team_role_ids'];
            $tickeTeamRoles = collect($ticketTeamRoleIds)->map(fn ($ticket_team_role_id) => [
                'role_id' => $ticket_team_role_id,
                'ticket_team_id' => $team->id,
            ]);
            TicketTeamRole::insert($tickeTeamRoles->toArray());
        }

        return TicketTeamData::from($team)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketTeam $team): bool
    {
        if (! request()->user()?->can('ticketTeam.delete')) {
            abort(403);
        }

        $team->ticketTeamRoles()->delete();

        return $team->delete() ?? false;
    }
}
