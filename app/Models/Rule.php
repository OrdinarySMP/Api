<?php

namespace App\Models;

use Database\Factories\RuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $number
 * @property string $name
 * @property string $rule
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\RuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Rule extends Model
{
    /** @use HasFactory<RuleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
