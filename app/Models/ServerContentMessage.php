<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $server_id
 * @property string $heading
 * @property string $not_recommended
 * @property string $recommended
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereNotRecommended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereRecommended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServerContentMessage whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ServerContentMessage extends Model
{
    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
