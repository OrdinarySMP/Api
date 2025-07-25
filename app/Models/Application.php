<?php

namespace App\Models;

use App\Enums\ApplicationResponseType;
use App\Enums\ApplicationRoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $guild_id
 * @property string $name
 * @property int $active
 * @property string $log_channel
 * @property string $accept_message
 * @property string $deny_message
 * @property string $confirmation_message
 * @property string $completion_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\ApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAcceptMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCompletionMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereConfirmationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereDenyMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereLogChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $acceptRemovalRoles
 * @property-read int|null $accept_removal_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationResponse> $acceptedResponses
 * @property-read int|null $accepted_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $acceptedRoles
 * @property-read int|null $accepted_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationQuestion> $applicationQuestions
 * @property-read int|null $application_questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationResponse> $applicationResponses
 * @property-read int|null $application_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $applicationRoles
 * @property-read int|null $application_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationSubmission> $applicationSubmissions
 * @property-read int|null $application_submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationResponse> $deniedResponses
 * @property-read int|null $denied_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $deniedRoles
 * @property-read int|null $denied_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $denyRemovalRoles
 * @property-read int|null $deny_removal_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $pendingRoles
 * @property-read int|null $pending_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $pingRoles
 * @property-read int|null $ping_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationRole> $restrictedRoles
 * @property-read int|null $restricted_roles_count
 *
 * @mixin \Eloquent
 */
class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<ApplicationQuestion, $this>
     */
    public function applicationQuestions(): HasMany
    {
        return $this->hasMany(ApplicationQuestion::class)->orderBy('order');
    }

    /**
     * @return HasMany<ApplicationSubmission, $this>
     */
    public function applicationSubmissions(): HasMany
    {
        return $this->hasMany(ApplicationSubmission::class);
    }

    /**
     * @return HasMany<ApplicationResponse, $this>
     */
    public function applicationResponses(): HasMany
    {
        return $this->hasMany(ApplicationResponse::class);
    }

    /**
     * @return HasMany<ApplicationResponse, $this>
     */
    public function acceptedResponses(): HasMany
    {
        return $this->applicationResponses()->where('type', ApplicationResponseType::Accepted);
    }

    /**
     * @return HasMany<ApplicationResponse, $this>
     */
    public function deniedResponses(): HasMany
    {
        return $this->applicationResponses()->where('type', ApplicationResponseType::Denied);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function applicationRoles(): HasMany
    {
        return $this->hasMany(ApplicationRole::class);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function restrictedRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Restricted);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function acceptedRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Accepted);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function deniedRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Denied);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function pingRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Ping);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function acceptRemovalRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::AcceptRemoval);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function denyRemovalRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::DenyRemoval);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function pendingRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Pending);
    }

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function requiredRoles(): HasMany
    {
        return $this->applicationRoles()->where('type', ApplicationRoleType::Required);
    }
}
