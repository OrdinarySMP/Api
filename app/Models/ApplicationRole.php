<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationRole extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationRoleFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
