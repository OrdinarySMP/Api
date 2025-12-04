<?php

namespace App\Http\Controllers;

use App\Data\ApplicationSubmissionData;
use App\Http\Requests\ApplicationSubmission\StoreRequest;
use App\Http\Requests\ApplicationSubmission\UpdateRequest;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;
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
    public function index(): PaginatedDataCollection
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
    public function store(StoreRequest $request): ApplicationSubmissionData
    {
        return ApplicationSubmissionData::from(ApplicationSubmission::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationSubmission $applicationSubmission): ApplicationSubmissionData
    {
        $applicationSubmission->update($request->validated());

        return ApplicationSubmissionData::from($applicationSubmission->refresh())->wrap('data');
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

    /**
     * @return DataCollection<array-key, ApplicationSubmissionData>
     */
    public function history(ApplicationSubmission $applicationSubmission): DataCollection
    {
        $history = ApplicationSubmission::with('application', 'applicationResponse')
            ->where('discord_id', $applicationSubmission->discord_id)
            ->get();

        return ApplicationSubmissionData::collect($history, DataCollection::class)->wrap('data');
    }
}
