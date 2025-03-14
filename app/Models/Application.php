<?php

namespace App\Models;

use App\Enums\ApplicationState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $guild_id
 * @property string $name
 * @property int $active
 * @property string $log_channel
 * @property string $accept_message
 * @property string $deny_message
 * @property string $confirmation_message
 * @property string $completion_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property ApplicationState $state
 *
 * @method static \Database\Factories\ApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAcceptMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCompletionMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereConfirmationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereDenyMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereLogChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    protected $casts = [
        'state' => ApplicationState::class,
    ];
}
