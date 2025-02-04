<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPanel extends Model
{
    /** @use HasFactory<\Database\Factories\TicketPanelFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
