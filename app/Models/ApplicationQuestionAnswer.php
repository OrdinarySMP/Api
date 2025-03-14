<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $application_question_id
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
 * @mixin \Eloquent
 */
class ApplicationQuestionAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationQuestionAnswerFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];
}
