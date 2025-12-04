<?php

use App\Models\Ticket;
use App\Models\TicketTranscript;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

test('can create ticket transcript', function () {
    Http::fake([
        config('services.discord.api_url').'/users/*' => Http::response([]),
    ]);

    $user = User::factory()->owner()->create();
    $ticket = Ticket::factory()->create();
    $data = [
        'ticket_id' => $ticket->id,
        'discord_user_id' => '100000000000000000',
        'message_id' => '100000000000000001',
        'message' => 'Hello Test!',
        'attachments' => '[{"id":1}]',
        'embeds' => '[{"id":2}]',
    ];

    $this->actingAs($user)
        ->postJson(route('transcript.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_transcripts', $data);
});

test('can udpate ticket transcript', function () {
    Http::fake([
        config('services.discord.api_url').'/users/*' => Http::response([]),
    ]);

    $ticketTranscript = TicketTranscript::factory()->create();

    $user = User::factory()->owner()->create();
    $ticket = Ticket::factory()->create();
    $data = [
        'ticket_id' => $ticket->id,
        'discord_user_id' => '100000000000000000',
        'message_id' => $ticketTranscript->message_id,
        'message' => 'Hello Test!',
        'attachments' => '[{"id":1}]',
        'embeds' => '[{"id":2}]',
    ];

    $this->actingAs($user)
        ->postJson(route('transcript.store'), $data)
        ->assertCreated()
        ->assertJson(['data' => $data]);

    $this->assertDatabaseHas('ticket_transcripts', [
        'id' => $ticketTranscript->id,
        ...$data,
    ]);
});

test('can delete ticket transcript', function () {
    Carbon::setTestNow();

    $ticketTranscript = TicketTranscript::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->deleteJson(route('transcript.delete', $ticketTranscript->message_id))
        ->assertOk();

    $this->assertDatabaseHas('ticket_transcripts', [
        'id' => $ticketTranscript->id,
        'deleted_at' => now(),
    ]);
});
