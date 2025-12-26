<?php

namespace App\Models;

use Database\Factories\ApplicationQuestionAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $application_question_id
 * @property string $answer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $application_submission_id
 * @property string|null $attachments
 *
 * @method static \Database\Factories\ApplicationQuestionAnswerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereApplicationQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereApplicationSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationQuestionAnswer whereUpdatedAt($value)
 *
 * @property-read ApplicationQuestion|null $applicationQuestion
 * @property-read ApplicationSubmission|null $applicationSubmission
 *
 * @mixin \Eloquent
 */
class ApplicationQuestionAnswer extends Model
{
    /** @use HasFactory<ApplicationQuestionAnswerFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo<ApplicationQuestion, $this>
     */
    public function applicationQuestion(): BelongsTo
    {
        return $this->belongsTo(ApplicationQuestion::class)->withTrashed();
    }

    /**
     * @return BelongsTo<ApplicationSubmission, $this>
     */
    public function applicationSubmission(): BelongsTo
    {
        return $this->belongsTo(ApplicationSubmission::class);
    }
}
