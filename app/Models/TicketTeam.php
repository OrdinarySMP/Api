<?php

namespace App\Models;

use Database\Factories\TicketTeamFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, TicketButton> $ticketButtons
 * @property-read int|null $ticket_buttons_count
 * @property-read Collection<int, TicketTeamRole> $ticketTeamRoles
 * @property-read int|null $ticket_team_roles_count
 *
 * @method static \Database\Factories\TicketTeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketTeam whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketTeam extends Model
{
    /** @use HasFactory<TicketTeamFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return HasMany<TicketButton, $this>
     */
    public function ticketButtons(): HasMany
    {
        return $this->hasMany(TicketButton::class);
    }

    /**
     * @return HasMany<TicketTeamRole, $this>
     */
    public function ticketTeamRoles(): HasMany
    {
        return $this->hasMany(TicketTeamRole::class);
    }
}
