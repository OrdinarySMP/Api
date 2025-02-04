<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTeam extends Model
{
    /** @use HasFactory<\Database\Factories\TicketTeamFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
