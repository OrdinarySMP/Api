<?php

namespace App\Models;

use Database\Factories\TicketPanelFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string $embed_color
 * @property string $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, TicketButton> $ticketButtons
 * @property-read int|null $ticket_buttons_count
 *
 * @method static \Database\Factories\TicketPanelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereEmbedColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPanel whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketPanel extends Model
{
    /** @use HasFactory<TicketPanelFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return HasMany<TicketButton, $this>
     */
    public function ticketButtons(): HasMany
    {
        return $this->hasMany(TicketButton::class);
    }
}
