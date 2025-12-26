<?php

namespace App\Models;

use Database\Factories\TicketTranscriptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $ticket_id
 * @property string $discord_user_id
 * @property string $message_id
 * @property string|null $message
 * @property string|null $attachments
 * @property string|null $embeds
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Ticket|null $ticket
 *
 * @method static \Database\Factories\TicketTranscriptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereDiscordUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereEmbeds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTranscript whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketTranscript extends Model
{
    /** @use HasFactory<TicketTranscriptFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
