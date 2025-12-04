<?php

namespace App\Http\Controllers;

use App\Data\TicketConfigData;
use App\Enums\DiscordButton;
use App\Http\Requests\TicketConfig\SetupRequest;
use App\Http\Requests\TicketConfig\StoreRequest;
use App\Models\TicketButton;
use App\Models\TicketConfig;
use App\Models\TicketPanel;
use App\Models\TicketTeam;
use App\Repositories\TicketPanelRepository;
use Illuminate\Support\Facades\DB;

class TicketConfigController extends Controller
{
    public function __construct(
        protected TicketPanelRepository $ticketPanelRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): TicketConfigData
    {
        if (! request()->user()?->can('ticketConfig.read')) {
            abort(403);
        }

        $guild_id = config('services.discord.server_id');

        if (request()->user()->hasRole('Bot')) {
            $guild_id = request()->input('filter[guild_id]', $guild_id);
        }

        return TicketConfigData::from(TicketConfig::where('guild_id', $guild_id)->first())->wrap('data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketConfigData
    {
        TicketConfig::upsert([
            [
                ...$request->validated(),
                'guild_id' => config('services.discord.server_id'),
            ],
        ], uniqueBy: ['guild_id'], update: ['category_id', 'transcript_channel_id']);

        return TicketConfigData::from(TicketConfig::where('guild_id', config('services.discord.server_id'))->first())->wrap('data');
    }

    public function setup(SetupRequest $request): true
    {
        if (! request()->user()?->hasRole('Bot')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $ticketConfig = TicketConfig::create($request->validated());

            $ticketTeam = TicketTeam::create(['name' => 'default']);

            $ticketPanel = TicketPanel::create([
                'title' => 'Click to open a ticket',
                'message' => 'Click on the button corresponding to the type of ticket you wish to open',
                'embed_color' => '#22e629',
                'channel_id' => $request->validated('create_channel_id'),
            ]);

            $ticketButton = TicketButton::create([
                'text' => 'Staff support',
                'color' => DiscordButton::Success,
                'initial_message' => "Thank you for contacting our support.\nPlease describe your issue and wait for a response.",
                'emoji' => '⚠️',
                'naming_scheme' => '%id%-staff-support',
                'ticket_team_id' => $ticketTeam->id,
                'ticket_panel_id' => $ticketPanel->id,
            ]);

            $this->ticketPanelRepository->sendPanel($ticketPanel);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('An error occurred while setting up the ticket system. Please try again later. If this error persists, please report to the staff team.');
        }
        DB::commit();

        return true;
    }
}
