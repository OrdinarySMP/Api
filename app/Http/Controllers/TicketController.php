<?php

namespace App\Http\Controllers;

use App\Enums\TicketState;
use App\Http\Requests\Ticket\CloseRequest;
use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketButton;
use App\Repositories\TicketRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
    public function __construct(
        protected TicketRepository $ticketRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('ticket.read')) {
            abort(403);
        }
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedIncludes(['ticketButton', 'ticketTranscripts'])
            ->allowedSorts('created_at')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
            ])
            ->getOrPaginate();

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketResource
    {
        /**
         * @var TicketButton $ticketButton
         */
        $ticketButton = TicketButton::find($request->validated('ticket_button_id'));
        /**
         * @var array{ticket_button_id:string,created_by_discord_user_id:string}
         */
        $data = $request->validated();
        $ticket = $this->ticketRepository->createForButton($ticketButton, $data);

        $this->ticketRepository->pingTeams($ticket);
        $this->ticketRepository->sendInitialMessage($ticket);

        return new TicketResource($ticket);
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
