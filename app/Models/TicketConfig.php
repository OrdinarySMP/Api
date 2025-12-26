<?php

namespace App\Models;

use Database\Factories\TicketConfigFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $guild_id
 * @property string $category_id
 * @property string $transcript_channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
    /** @use HasFactory<TicketConfigFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
