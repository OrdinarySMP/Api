<?php

namespace App\Models;

use App\Enums\ApplicationResponseType;
use App\Enums\ApplicationRoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property ApplicationState $state
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
 * @mixin \Eloquent
 */
class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return HasMany<ApplicationQuestion, $this>
     */
    public function applicationQuestions(): HasMany
    {
        return $this->hasMany(ApplicationQuestion::class);
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
}
