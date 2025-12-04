<?php

namespace App\Models;

use App\Enums\ApplicationRoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $application_id
 * @property string $role_id
 * @property ApplicationRoleType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Application|null $application
 *
 * @method static \Database\Factories\ApplicationRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationRole whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ApplicationRole extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'type' => ApplicationRoleType::class,
    ];

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
