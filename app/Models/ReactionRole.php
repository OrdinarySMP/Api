<?php

namespace App\Models;

use Database\Factories\ReactionRoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $message_id
 * @property string $channel_id
 * @property string $emoji
 * @property string $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\ReactionRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReactionRole whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ReactionRole extends Model
{
    /** @use HasFactory<ReactionRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
