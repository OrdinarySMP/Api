<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $question
 * @property int $order
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $application_id
 *
 * @method static \Database\Factories\ApplicationQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestion whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplicationQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationQuestionFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
