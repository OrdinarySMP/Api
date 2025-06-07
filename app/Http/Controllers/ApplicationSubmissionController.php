<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationSubmission\StoreRequest;
use App\Http\Requests\ApplicationSubmission\UpdateRequest;
use App\Http\Resources\ApplicationSubmissionResource;
use App\Models\ApplicationSubmission;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('applicationSubmission.read')) {
            abort(403);
        }
        $applicationSubmissions = QueryBuilder::for(ApplicationSubmission::class)
            ->allowedIncludes([
                'applicationQuestionAnswers.applicationQuestion',
                'application',
            ])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('discord_id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('application_id'),
            ])
            ->allowedSorts([
                'submitted_at',
            ])
            ->getOrPaginate();

        return ApplicationSubmissionResource::collection($applicationSubmissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationSubmissionResource
    {
        return new ApplicationSubmissionResource(ApplicationSubmission::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationSubmission $applicationSubmission): ApplicationSubmissionResource
    {
        $applicationSubmission->update($request->validated());

        return new ApplicationSubmissionResource($applicationSubmission->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationSubmission $applicationSubmission): bool
    {
        if (! request()->user()?->can('applicationSubmission.delete')) {
            abort(403);
        }

        if ($applicationSubmission->message_id) {
            $response = Http::discordBot()->delete('/channels/'.$applicationSubmission->channel_id.'/messages/'.$applicationSubmission->message_id);
            if ($response->ok()) {
                Log::error('Could not delete submission:', $response->json());
            }
        }

        return $applicationSubmission->delete() ?? false;
    }
}
