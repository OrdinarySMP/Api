<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketButton\StoreRequest;
use App\Http\Requests\TicketButton\UpdateRequest;
use App\Models\TicketButton;
use App\Resource\TicketButtonResource;

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
            ->allowedFilters([
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        return TicketButtonResource::collection($ticketButtons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        return new TicketButtonResource(TicketButton::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketButton $ticketButton)
    {
        $ticketButton->update($request->validated());

        return new TicketButtonResource($ticketButton);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketButton $ticketButton)
    {
        if (! request()->user()?->can('ticketButton.delete')) {
            abort(403);
        }

        return $ticketButton->delete() ?? false;
    }
}
