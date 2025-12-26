<?php

namespace App\Http\Controllers;

use App\Data\Requests\CloseTicketRequest;
use App\Data\Requests\CreateTicketRequest;
use App\Data\Requests\ReadTicketRequest;
use App\Data\TicketData;
use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\TicketButton;
use App\Models\User;
use App\Repositories\TicketRepository;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;
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
     * @return PaginatedDataCollection<array-key, TicketData>|DataCollection<array-key, TicketData>
     */
    public function index(
        #[CurrentUser] User $user,
        ReadTicketRequest $request
    ): PaginatedDataCollection|DataCollection {
        $ticketsQuery = QueryBuilder::for(Ticket::class)
            ->allowedIncludes(['ticketButton.ticketTeam.ticketTeamRoles', 'ticketTranscripts'])
            ->allowedSorts('created_at')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
            ]);

        if ($user->cannot('ticket.read')) {
            $ticketsQuery->where('created_by_discord_user_id', $user->discord_id);
        }

        $tickets = $ticketsQuery->getOrPaginate();

        if (request()->has('full')) {
            return TicketData::collect($tickets, DataCollection::class)->wrap('data');
        }

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

    public function close(CloseTicketRequest $request, Ticket $ticket): bool
    {
        $ticket->closed_by_discord_user_id = $request->closed_by_discord_user_id;
        $ticket->closed_reason = $request->closed_reason;
        $ticket->state = TicketState::Closed;
        $ticket->save();

        $response = Http::discordBot()->delete('/channels/'.$ticket->channel_id);
        $this->ticketRepository->sendTranscript($ticket);

        return $response->ok();
    }
}
