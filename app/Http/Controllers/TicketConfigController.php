<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketConfig\StoreRequest;
use App\Http\Requests\UpdateTicketConfigRequest;
use App\Models\TicketConfig;

class TicketConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (! request()->user()?->can('ticketConfig.read')) {
            abort(403);
        }

        $guild_id = config('services.discord.server_id');

        if (request()->user()->hasRole('Bot')) {
            $guild_id = request()->input('filter[guild_id]', $guild_id);
        }
        return TicketConfig::where('guild_id', $guild_id)->first();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        TicketConfig::upsert([
            [
                ...$request->validated(),
                'guild_id' => config('services.discord.server_id'),
            ],
        ], uniqueBy: ['guild_id'], update: ['category_id', 'transcript_channel_id', 'create_channel_id']);

        return TicketConfig::where('guild_id', config('services.discord.server_id'))->firstOrFail();
    }
}
