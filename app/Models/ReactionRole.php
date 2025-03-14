<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $message_id
 * @property string $channel_id
 * @property string $emoji
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    /** @use HasFactory<\Database\Factories\ReactionRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
