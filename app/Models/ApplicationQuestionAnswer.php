<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationQuestionAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationQuestionAnswerFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return BelongsTo<ApplicationQuestion, $this>
     */
    public function applicationQuestion(): BelongsTo
    {
        return $this->belongsTo(ApplicationQuestion::class);
    }
}
