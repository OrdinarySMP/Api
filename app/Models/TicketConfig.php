<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $guild_id
 * @property string $category_id
 * @property string $transcript_channel_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\TicketConfigFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereTranscriptChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketConfig whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TicketConfig extends Model
{
    /** @use HasFactory<\Database\Factories\TicketConfigFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
