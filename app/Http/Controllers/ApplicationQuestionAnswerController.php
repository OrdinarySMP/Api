<?php

namespace App\Http\Controllers;

use App\Data\ApplicationQuestionAnswerData;
use App\Enums\ApplicationSubmissionState;
use App\Http\Requests\ApplicationQuestionAnswer\StoreRequest;
use App\Http\Requests\ApplicationQuestionAnswer\UpdateRequest;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationSubmission;
use App\Repositories\ApplicationActivityRepository;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationQuestionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ApplicationQuestionAnswerData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('applicationQuestionAnswer.read')) {
            abort(403);
        }
        $applicationQuestionAnswer = QueryBuilder::for(ApplicationQuestionAnswer::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('application_id'),
            ])
            ->getOrPaginate();

        return ApplicationQuestionAnswerData::collect($applicationQuestionAnswer, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationQuestionAnswerData
    {
        $data = $request->validated();

        /**
         * @var ApplicationSubmission
         */
        $applicationSubmission = ApplicationSubmission::find($data['application_submission_id']);
        if ($applicationSubmission->state === ApplicationSubmissionState::Cancelled) {
            abort(403, 'Application was cancelled.');
        }
        $applicationQuestionAnswer = ApplicationQuestionAnswer::create($data);
        (new ApplicationActivityRepository)->questionAnswerCreated($applicationQuestionAnswer);

        return ApplicationQuestionAnswerData::from($applicationQuestionAnswer)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationQuestionAnswer $applicationQuestionAnswer): ApplicationQuestionAnswerData
    {
        $applicationQuestionAnswer->update($request->validated());

        return ApplicationQuestionAnswerData::from($applicationQuestionAnswer->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationQuestionAnswer $applicationQuestionAnswer): bool
    {
        if (! request()->user()?->can('applicationQuestionAnswer.delete')) {
            abort(403);
        }

        return $applicationQuestionAnswer->delete() ?? false;
    }
}
