<?php

namespace App\Models;

use App\Enums\ApplicationSubmissionState;
use App\Observers\ApplicationSubmissionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ApplicationSubmissionObserver::class])]
/**
 * @property int $id
 * @property string $discord_id
 * @property string|null $submitted_at
 * @property ApplicationSubmissionState $state
 * @property int|null $application_response_id
 * @property string|null $custom_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $message_id
 * @property string|null $channel_id
 * @property string|null $handled_by
 * @property int|null $application_id
 * @property-read \App\Models\Application|null $application
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationQuestionAnswer> $applicationQuestionAnswers
 * @property-read int|null $application_question_answers_count
 * @property-read \App\Models\ApplicationResponse|null $applicationResponse
 *
 * @method static \Database\Factories\ApplicationSubmissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereApplicationResponseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereCustomResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereDiscordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereHandledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSubmission whereUpdatedAt($value)
 * @method static Builder<static>|ApplicationSubmission completed()
 *
 * @mixin \Eloquent
 */
class ApplicationSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationSubmissionFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'state' => ApplicationSubmissionState::class,
        'submitted_at' => 'datetime',
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
        return $this->belongsTo(ApplicationResponse::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class)->withTrashed();
    }

    /**
     * @param  Builder<ApplicationSubmission>  $query
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->whereIn('state', [ApplicationSubmissionState::Pending->value, ApplicationSubmissionState::Accepted->value, ApplicationSubmissionState::Denied->value]);
    }
}
