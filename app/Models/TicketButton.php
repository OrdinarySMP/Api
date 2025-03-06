<?php

namespace App\Models;

use App\Enums\DiscordButton;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketButton extends Model
{
    /** @use HasFactory<\Database\Factories\TicketButtonFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

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
