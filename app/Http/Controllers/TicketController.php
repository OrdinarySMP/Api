<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateTicketRequest;
use App\Data\TicketData;
use App\Enums\TicketState;
use App\Http\Requests\Ticket\CloseRequest;
use App\Models\Ticket;
use App\Models\TicketButton;
use App\Repositories\TicketRepository;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
    public function __construct(
        protected TicketRepository $ticketRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, TicketData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('ticket.read')) {
            abort(403);
        }
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedIncludes(['ticketButton.ticketTeam.ticketTeamRoles', 'ticketTranscripts'])
            ->allowedSorts('created_at')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
            ])
            ->getOrPaginate();

        return TicketData::collect($tickets, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request): TicketData
    {
        /**
         * @var TicketButton $ticketButton
         */
        $ticketButton = TicketButton::find($request->ticket_button_id);
        $ticket = $this->ticketRepository->createForButton($ticketButton, $request);

        $this->ticketRepository->pingRoles($ticket);
        $this->ticketRepository->sendInitialMessage($ticket);

        return TicketData::from($ticket)->wrap('data');
    }

    public function close(CloseRequest $request, Ticket $ticket): bool
    {
        $ticket->update([
            ...$request->validated(),
            'state' => TicketState::Closed,
        ]);
        $response = Http::discordBot()->delete('/channels/'.$ticket->channel_id);
        $this->ticketRepository->sendTranscript($ticket);

        return $response->ok();
    }
}
