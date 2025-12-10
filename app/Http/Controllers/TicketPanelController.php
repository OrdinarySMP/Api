<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateTicketPanelRequest;
use App\Data\Requests\DeleteTicketPanelRequest;
use App\Data\Requests\ReadTicketPanelRequest;
use App\Data\Requests\SendTicketPanelRequest;
use App\Data\Requests\UpdateTicketPanelRequest;
use App\Data\TicketPanelData;
use App\Models\TicketPanel;
use App\Repositories\TicketPanelRepository;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;
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
    public function index(ReadTicketPanelRequest $request): PaginatedDataCollection|DataCollection
    {
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
    public function store(CreateTicketPanelRequest $request): TicketPanelData
    {
        $ticketPanel = new TicketPanel;
        $ticketPanel->title = $request->title;
        $ticketPanel->message = $request->message;
        $ticketPanel->embed_color = $request->embed_color;
        $ticketPanel->channel_id = $request->channel_id;
        $ticketPanel->save();

        return TicketPanelData::from($ticketPanel)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketPanelRequest $request, TicketPanel $panel): TicketPanelData
    {
        if (! $request->title instanceof Optional) {
            $panel->title = $request->title;
        }

        if (! $request->message instanceof Optional) {
            $panel->message = $request->message;
        }

        if (! $request->embed_color instanceof Optional) {
            $panel->embed_color = $request->embed_color;
        }

        if (! $request->channel_id instanceof Optional) {
            $panel->channel_id = $request->channel_id;
        }

        if ($panel->isDirty()) {
            $panel->save();
        }

        return TicketPanelData::from($panel)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteTicketPanelRequest $request, TicketPanel $panel): bool
    {
        if (! request()->user()?->can('ticketPanel.delete')) {
            abort(403);
        }

        return $panel->delete() ?? false;
    }

    public function send(SendTicketPanelRequest $request, TicketPanel $panel): bool
    {
        return $this->ticketPanelRepository->sendPanel($panel);
    }
}
