<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketButtonPingRole extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTeamRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return BelongsTo<TicketButton, $this>
     */
    public function ticketButton(): BelongsTo
    {
        return $this->belongsTo(TicketButton::class);
    }
}
