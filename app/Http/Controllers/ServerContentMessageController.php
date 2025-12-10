<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateServerContentMessageRequest;
use App\Data\Requests\ReadServerContentMessageRequest;
use App\Data\ServerContentMessageData;
use App\Models\ServerContentMessage;

class ServerContentMessageController extends Controller
{
    public function index(ReadServerContentMessageRequest $request): ?ServerContentMessageData
    {
        if (! request()->user()?->can('serverContentMessage.read')) {
            abort(403);
        }

        $messages = ServerContentMessage::where('server_id', config('services.discord.server_id'))->first();

        return $messages ? ServerContentMessageData::from($messages) : null;
    }

    public function store(CreateServerContentMessageRequest $request): ServerContentMessageData
    {
        ServerContentMessage::upsert([
            [
                'heading' => $request->heading,
                'not_recommended' => $request->not_recommended,
                'recommended' => $request->recommended,
                'server_id' => config('services.discord.server_id'),
            ],
        ], uniqueBy: ['server_id'], update: ['heading', 'not_recommended', 'recommended']);

        return ServerContentMessageData::from(
            ServerContentMessage::where('server_id', config('services.discord.server_id'))->firstOrFail()
        );
    }
}
