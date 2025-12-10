<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateTicketTranscriptRequest;
use App\Data\Requests\DeleteTicketTranscriptRequest;
use App\Data\TicketTranscriptData;
use App\Models\TicketTranscript;

class TicketTranscriptController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketTranscriptRequest $request): TicketTranscriptData
    {
        TicketTranscript::upsert([
            'ticket_id' => $request->ticket_id,
            'discord_user_id' => $request->discord_user_id,
            'message_id' => $request->message_id,
            'message' => $request->message,
            'attachments' => $request->attachments,
            'embeds' => $request->embeds,
        ], uniqueBy: ['message_id'], update: ['ticket_id', 'discord_user_id', 'message', 'attachments', 'embeds']);

        return TicketTranscriptData::from(TicketTranscript::where('message_id', $request->message_id)->first())->wrap('data');
    }

    public function delete(DeleteTicketTranscriptRequest $request, string $messageId): void
    {
        if (! request()->user()?->can('ticketTranscript.delete')) {
            abort(403);
        }
        TicketTranscript::where('message_id', $messageId)->update(['deleted_at' => now()]);
    }
}
