<?php

use App\Models\Ticket;
use App\Models\TicketTranscript;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\Traits\CrudPermissionTrait;

use function PHPUnit\Framework\assertFalse;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    Carbon::setTestNow();

    Http::fake([
        config('services.discord.api_url').'/users/*' => Http::response([]),
    ]);
});

describe('create operations', function () {
    test('create permission', function () {
        $ticket = Ticket::factory()->create();
        $data = [
            'ticket_id' => $ticket->id,
            'discord_user_id' => '100000000000000000',
            'message_id' => '100000000000000001',
            'message' => 'Hello Test!',
            'attachments' => '[{"id":1}]',
            'embeds' => '[{"id":2}]',
        ];
        $this->assertCreatePermissions('transcript.store', 'ticketTranscript.create', $data, TicketTranscript::class);
    });
    test('can create ticket transcript', function () {
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
});

describe('update operations', function () {
    test('can update ticket transcript', function () {
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
});

describe('delete operations', function () {
    test('delete permission', function () {
        $permission = 'ticketTranscript.delete';
        $route = 'transcript.delete';
        $table = TicketTranscript::class;

        $ticketTranscript = TicketTranscript::factory()->create();
        $user = User::factory()->create();
        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->deleteJson(route($route, $ticketTranscript->message_id))
            ->assertForbidden();
        $this->assertDatabaseHas($table, ['id' => $ticketTranscript->id]);

        $user->givePermissionTo($permission);
        $this->actingAs($user)
            ->deleteJson(route($route, $ticketTranscript->message_id))
            ->assertOk();
        $this->assertDatabaseHas($table, [
            'id' => $ticketTranscript->id,
            'deleted_at' => now(),
        ]);
    });

    test('can delete ticket transcript', function () {

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
});
