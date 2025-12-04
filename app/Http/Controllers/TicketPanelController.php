<?php

namespace App\Http\Controllers;

use App\Data\TicketPanelData;
use App\Http\Requests\TicketPanel\StoreRequest;
use App\Http\Requests\TicketPanel\UpdateRequest;
use App\Models\TicketPanel;
use App\Repositories\TicketPanelRepository;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketPanelController extends Controller
{
    public function __construct(
        protected TicketPanelRepository $ticketPanelRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, TicketPanelData>|DataCollection<array-key, TicketPanelData>
     */
    public function index(): PaginatedDataCollection|DataCollection
    {
        if (! request()->user()?->can('ticketPanel.read')) {
            abort(403);
        }
        $ticketPanels = QueryBuilder::for(TicketPanel::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        if (request()->has('full')) {
            return TicketPanelData::collect($ticketPanels, DataCollection::class)->wrap('data');
        }

        return TicketPanelData::collect($ticketPanels, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketPanelData
    {
        return TicketPanelData::from(TicketPanel::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, TicketPanel $panel): TicketPanelData
    {
        $panel->update($request->validated());

        return TicketPanelData::from($panel)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketPanel $panel): bool
    {
        if (! request()->user()?->can('ticketPanel.delete')) {
            abort(403);
        }

        return $panel->delete() ?? false;
    }

    public function send(TicketPanel $panel): bool
    {
        return $this->ticketPanelRepository->sendPanel($panel);
    }
}
