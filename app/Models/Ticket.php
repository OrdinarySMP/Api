<?php

namespace App\Models;

use App\Enums\TicketState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $ticket_button_id
 * @property string|null $channel_id
 * @property TicketState $state
 * @property string $created_by_discord_user_id
 * @property string|null $closed_by_discord_user_id
 * @property string|null $closed_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TicketButton|null $ticketButton
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketTranscript> $ticketTranscripts
 * @property-read int|null $ticket_transcripts_count
 *
 * @method static \Database\Factories\TicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereClosedByDiscordUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereClosedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedByDiscordUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereTicketButtonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'state' => TicketState::class,
    ];

    /**
     * @return BelongsTo<TicketButton, $this>
     */
    public function ticketButton(): BelongsTo
    {
        return $this->belongsTo(TicketButton::class);
    }

    /**
     * @return HasMany<TicketTranscript, $this>
     */
    public function ticketTranscripts(): HasMany
    {
        return $this->hasMany(TicketTranscript::class);
    }
}
