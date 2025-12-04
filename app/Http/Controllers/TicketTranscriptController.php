<?php

namespace App\Http\Controllers;

use App\Data\TicketTranscriptData;
use App\Http\Requests\TicketTranscript\StoreRequest;
use App\Models\TicketTranscript;

class TicketTranscriptController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): TicketTranscriptData
    {
        TicketTranscript::upsert([
            $request->validated(),
        ], uniqueBy: ['message_id'], update: ['ticket_id', 'discord_user_id', 'message', 'attachments', 'embeds']);

        return TicketTranscriptData::from(TicketTranscript::where('message_id', $request->validated('message_id'))->first())->wrap('data');
    }

    public function delete(string $messageId): void
    {
        if (! request()->user()?->can('ticketTranscript.delete')) {
            abort(403);
        }
        TicketTranscript::where('message_id', $messageId)->update(['deleted_at' => now()]);
    }
}
