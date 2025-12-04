<?php

namespace App\Models;

use App\Enums\DiscordButton;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $ticket_team_id
 * @property int $ticket_panel_id
 * @property string $text
 * @property DiscordButton $color
 * @property string $initial_message
 * @property string $emoji
 * @property string $naming_scheme
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $disabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketButtonPingRole> $ticketButtonPingRoles
 * @property-read int|null $ticket_button_ping_roles_count
 * @property-read \App\Models\TicketPanel|null $ticketPanel
 * @property-read \App\Models\TicketTeam|null $ticketTeam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 *
 * @method static \Database\Factories\TicketButtonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereInitialMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereNamingScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereTicketPanelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereTicketTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButton whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketButton extends Model
{
    /** @use HasFactory<\Database\Factories\TicketButtonFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'color' => DiscordButton::class,
        'disabled' => 'boolean',
    ];

    /**
     * @return BelongsTo<TicketPanel, $this>
     */
    public function ticketPanel(): BelongsTo
    {
        return $this->belongsTo(TicketPanel::class);
    }

    /**
     * @return BelongsTo<TicketTeam, $this>
     */
    public function ticketTeam(): BelongsTo
    {
        return $this->belongsTo(TicketTeam::class);
    }

    /**
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return HasMany<TicketButtonPingRole, $this>
     */
    public function ticketButtonPingRoles(): HasMany
    {
        return $this->hasMany(TicketButtonPingRole::class);
    }
}
