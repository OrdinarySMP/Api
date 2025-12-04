<?php

namespace App\Http\Controllers;

use App\Data\ServerContentMessageData;
use App\Http\Requests\ServerContentMessage\CreateRequest;
use App\Models\ServerContentMessage;

class ServerContentMessageController extends Controller
{
    public function index(): ?ServerContentMessageData
    {
        if (! request()->user()?->can('serverContentMessage.read')) {
            abort(403);
        }

        $messages = ServerContentMessage::where('server_id', config('services.discord.server_id'))->first();

        return $messages ? ServerContentMessageData::from($messages) : null;
    }

    public function store(CreateRequest $request): ServerContentMessageData
    {
        ServerContentMessage::upsert([
            [
                ...$request->validated(),
                'server_id' => config('services.discord.server_id'),
            ],
        ], uniqueBy: ['server_id'], update: ['heading', 'not_recommended', 'recommended']);

        return ServerContentMessageData::from(
            ServerContentMessage::where('server_id', config('services.discord.server_id'))->firstOrFail()
        );
    }
}
