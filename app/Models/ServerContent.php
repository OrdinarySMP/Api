<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feed
 *
 * @property string $heading
 * @property string $not_recommended
 * @property string $recommended
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $description
 * @property bool $is_recommended
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\ServerContentFactory factory($count = null, $state = [])
 * @method static Builder<static>|ServerContent newModelQuery()
 * @method static Builder<static>|ServerContent newQuery()
 * @method static Builder<static>|ServerContent notRecommended()
 * @method static Builder<static>|ServerContent query()
 * @method static Builder<static>|ServerContent recommended()
 * @method static Builder<static>|ServerContent whereCreatedAt($value)
 * @method static Builder<static>|ServerContent whereDescription($value)
 * @method static Builder<static>|ServerContent whereId($value)
 * @method static Builder<static>|ServerContent whereIsRecommended($value)
 * @method static Builder<static>|ServerContent whereName($value)
 * @method static Builder<static>|ServerContent whereUpdatedAt($value)
 * @method static Builder<static>|ServerContent whereUrl($value)
 *
 * @mixin \Eloquent
 */
class ServerContent extends Model
{
    /** @use HasFactory<\Database\Factories\ServerContentFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    protected $casts = [
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @param  Builder<ServerContent>  $query
     */
    public function scopeNotRecommended(Builder $query): void
    {
        $query->where('is_recommended', false);
    }

    /**
     * @param  Builder<ServerContent>  $query
     */
    public function scopeRecommended(Builder $query): void
    {
        $query->where('is_recommended', true);
    }

    /**
     * @param  Builder<ServerContent>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
