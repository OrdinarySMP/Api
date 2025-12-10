<?php

namespace App\Http\Controllers;

use App\Data\Requests\ReadTicketConfigRequest;
use App\Data\Requests\SetupTicketConfigRequest;
use App\Data\Requests\UpdateTicketConfigRequest;
use App\Data\TicketConfigData;
use App\Enums\DiscordButton;
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
    public function index(ReadTicketConfigRequest $request): TicketConfigData
    {
        $guild_id = config('services.discord.server_id');

        if (request()->user()?->hasRole('Bot')) {
            $guild_id = request()->input('filter[guild_id]', $guild_id);
        }

        return TicketConfigData::from(TicketConfig::where('guild_id', $guild_id)->first())->wrap('data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateTicketConfigRequest $request): TicketConfigData
    {
        TicketConfig::upsert([
            [
                'category_id' => $request->category_id,
                'transcript_channel_id' => $request->transcript_channel_id,
                'guild_id' => config('services.discord.server_id'),
            ],
        ], uniqueBy: ['guild_id'], update: ['category_id', 'transcript_channel_id']);

        return TicketConfigData::from(TicketConfig::where('guild_id', config('services.discord.server_id'))->first())->wrap('data');
    }

    public function setup(SetupTicketConfigRequest $request): true
    {
        DB::beginTransaction();
        try {
            $ticketConfig = new TicketConfig;
            $ticketConfig->category_id = $request->category_id;
            $ticketConfig->transcript_channel_id = $request->transcript_channel_id;
            $ticketConfig->guild_id = $request->guild_id;
            $ticketConfig->save();

            $ticketTeam = TicketTeam::create(['name' => 'default']);

            $ticketPanel = TicketPanel::create([
                'title' => 'Click to open a ticket',
                'message' => 'Click on the button corresponding to the type of ticket you wish to open',
                'embed_color' => '#22e629',
                'channel_id' => $request->create_channel_id,
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
