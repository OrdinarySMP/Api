<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $ticket_team_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TicketTeam|null $ticketTeamRoles
 *
 * @method static \Database\Factories\TicketTeamRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole whereTicketTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeamRole whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketTeamRole extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTeamRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return BelongsTo<TicketTeam, $this>
     */
    public function ticketTeamRoles(): BelongsTo
    {
        return $this->belongsTo(TicketTeam::class);
    }
}
