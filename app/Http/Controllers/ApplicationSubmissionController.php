<?php

namespace App\Http\Controllers;

use App\Data\ApplicationSubmissionData;
use App\Data\Requests\CreateApplicationSubmissionRequest;
use App\Data\Requests\DeleteApplicationSubmissionRequest;
use App\Data\Requests\ReadApplicationSubmissionRequest;
use App\Data\Requests\UpdateApplicationSubmissionRequest;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ApplicationSubmissionData>
     */
    public function index(ReadApplicationSubmissionRequest $request): PaginatedDataCollection
    {
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
                'id',
                'state',
                'created_at',
                'updated_at',
                'submitted_at',
            ])
            ->getOrPaginate();

        return ApplicationSubmissionData::collect($applicationSubmissions, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateApplicationSubmissionRequest $request): ApplicationSubmissionData
    {
        $applicationSubmission = new ApplicationSubmission;
        $applicationSubmission->discord_id = $request->discord_id;
        $applicationSubmission->submitted_at = $request->submitted_at;
        $applicationSubmission->application_response_id = $request->application_response_id;
        $applicationSubmission->state = $request->state;
        $applicationSubmission->custom_response = $request->custom_response;
        $applicationSubmission->handled_by = $request->handled_by;
        $applicationSubmission->application_id = $request->application_id;
        $applicationSubmission->save();

        return ApplicationSubmissionData::from($applicationSubmission)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationSubmissionRequest $request, ApplicationSubmission $applicationSubmission): ApplicationSubmissionData
    {
        if (! $request->discord_id instanceof Optional) {
            $applicationSubmission->discord_id = $request->discord_id;
        }

        if (! $request->submitted_at instanceof Optional) {
            $applicationSubmission->submitted_at = $request->submitted_at;
        }

        if (! $request->application_response_id instanceof Optional) {
            $applicationSubmission->application_response_id = $request->application_response_id;
        }

        if (! $request->state instanceof Optional) {
            $applicationSubmission->state = $request->state;
        }

        if (! $request->custom_response instanceof Optional) {
            $applicationSubmission->custom_response = $request->custom_response;
        }

        if (! $request->handled_by instanceof Optional) {
            $applicationSubmission->handled_by = $request->handled_by;
        }

        if (! $request->application_id instanceof Optional) {
            $applicationSubmission->application_id = $request->application_id;
        }

        if ($applicationSubmission->isDirty()) {
            $applicationSubmission->save();
        }

        return ApplicationSubmissionData::from($applicationSubmission)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteApplicationSubmissionRequest $request, ApplicationSubmission $applicationSubmission): bool
    {
        if ($applicationSubmission->message_id) {
            $response = Http::discordBot()->delete('/channels/'.$applicationSubmission->channel_id.'/messages/'.$applicationSubmission->message_id);
            if ($response->ok()) {
                Log::error('Could not delete submission:', $response->json());
            }
        }

        return $applicationSubmission->delete() ?? false;
    }

    /**
     * @return DataCollection<array-key, ApplicationSubmissionData>
     */
    public function history(ReadApplicationSubmissionRequest $request, ApplicationSubmission $applicationSubmission): DataCollection
    {
        $history = ApplicationSubmission::with('application', 'applicationResponse')
            ->where('discord_id', $applicationSubmission->discord_id)
            ->get();

        return ApplicationSubmissionData::collect($history, DataCollection::class)->wrap('data');
    }
}
