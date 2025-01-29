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
        return TicketConfig::where('guild_id', config('services.discord.server_id'))->first();
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
