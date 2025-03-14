<?php

namespace App\Models;

use App\Enums\ApplicationResponseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property ApplicationResponseType $type
 * @property string $name
 * @property string $response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $application_id
 *
 * @method static \Database\Factories\ApplicationResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationResponse whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplicationResponse extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationResponseFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    protected $casts = [
        'type' => ApplicationResponseType::class,
    ];
}
