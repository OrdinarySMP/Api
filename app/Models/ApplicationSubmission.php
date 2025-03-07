<?php

namespace App\Models;

use App\Enums\ApplicationSubmissionState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationSubmissionFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    protected $casts = [
        'state' => ApplicationSubmissionState::class,
    ];

    /**
     * @return HasMany<ApplicationQuestionAnswer, $this>
     */
    public function applicationQuestionAnswers(): HasMany
    {
        return $this->hasMany(ApplicationQuestionAnswer::class);
    }

    /**
     * @return BelongsTo<ApplicationResponse, $this>
     */
    public function applicationResponse(): BelongsTo
    {
        return $this->belongsTo(ApplicationResponse::class);
    }
}
