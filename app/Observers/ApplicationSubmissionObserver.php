<?php

namespace App\Observers;

use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationSubmission;
use App\Repositories\ApplicationSubmissionRepository;

class ApplicationSubmissionObserver
{
    public function __construct(
        protected ApplicationSubmissionRepository $applicationSubmissionRepository
    ) {}

    /**
     * Handle the ApplicationSubmission "created" event.
     */
    public function created(ApplicationSubmission $applicationSubmission): void
    {
        ApplicationSubmission::withoutEvents(function () use ($applicationSubmission) {
            ApplicationSubmission::where('discord_id', $applicationSubmission->discord_id)
                ->where('state', ApplicationSubmissionState::InProgress)
                ->where('id', '!=', $applicationSubmission->id)
                ->update(['state' => ApplicationSubmissionState::Cancelled]);
        });
    }

    /**
     * Handle the ApplicationSubmission "updated" event.
     */
    public function updated(ApplicationSubmission $applicationSubmission): void
    {
        if ($applicationSubmission->state === ApplicationSubmissionState::Pending) {
            $this->applicationSubmissionRepository->sendApplicationSubmission($applicationSubmission);
        }
        if (
            $applicationSubmission->state === ApplicationSubmissionState::Accepted ||
            $applicationSubmission->state === ApplicationSubmissionState::Denied
        ) {
            $this->applicationSubmissionRepository->updateApplicationSubmission($applicationSubmission);
        }
    }

    /**
     * Handle the ApplicationSubmission "deleted" event.
     */
    public function deleted(ApplicationSubmission $applicationSubmission): void
    {
        //
    }

    /**
     * Handle the ApplicationSubmission "restored" event.
     */
    public function restored(ApplicationSubmission $applicationSubmission): void
    {
        //
    }

    /**
     * Handle the ApplicationSubmission "force deleted" event.
     */
    public function forceDeleted(ApplicationSubmission $applicationSubmission): void
    {
        //
    }
}
