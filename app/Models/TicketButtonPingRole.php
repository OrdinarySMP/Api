<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $ticket_button_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TicketButton|null $ticketButton
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole whereTicketButtonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketButtonPingRole whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketButtonPingRole extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTeamRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo<TicketButton, $this>
     */
    public function ticketButton(): BelongsTo
    {
        return $this->belongsTo(TicketButton::class);
    }
}
