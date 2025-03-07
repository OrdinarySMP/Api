<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'udpated_at'];

    /**
     * @return HasMany<ApplicationRole, $this>
     */
    public function applicationRoles(): HasMany
    {
        return $this->hasMany(ApplicationRole::class);
    }

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
}
