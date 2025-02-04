<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketPanelRequest;
use App\Http\Requests\UpdateTicketPanelRequest;
use App\Models\TicketPanel;

class TicketPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('ticketPanel.read')) {
            abort(403);
        }
        $ticketPanels = QueryBuilder::for(TicketPanel::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        return TicketPanelResource::collection($ticketPanels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        return new TicketPanelResource(TicketPanel::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketPanel $ticketPanel)
    {
        $ticketPanel->update($request->validated());

        return new TicketPanelResource($ticketPanel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketPanel $ticketPanel)
    {
        if (! request()->user()?->can('ticketPanel.delete')) {
            abort(403);
        }

        return $ticketPanel->delete() ?? false;
    }
}
