<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketTeam\StoreRequest;
use App\Http\Requests\TicketTeam\UpdateRequest;
use App\Http\Resources\TicketTeamResource;
use App\Models\TicketTeam;
use App\Models\TicketTeamRole;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
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

        return TicketTeamResource::collection($ticketTeams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketTeamResource
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

        return new TicketTeamResource($team);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketTeam $team): TicketTeamResource
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

        return new TicketTeamResource($team);
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
