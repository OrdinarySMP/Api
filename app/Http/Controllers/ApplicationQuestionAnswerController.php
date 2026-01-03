<?php

namespace App\Http\Controllers;

use App\Data\ApplicationQuestionAnswerData;
use App\Data\Requests\CreateApplicationQuestionAnswerRequest;
use App\Data\Requests\DeleteApplicationQuestionAnswerRequest;
use App\Data\Requests\ReadApplicationQuestionAnswerRequest;
use App\Data\Requests\UpdateApplicationQuestionAnswerRequest;
use App\Enums\ApplicationSubmissionState;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationSubmission;
use App\Repositories\ApplicationActivityRepository;
use Spatie\LaravelData\Optional;
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
    public function index(ReadApplicationQuestionAnswerRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateApplicationQuestionAnswerRequest $request): ApplicationQuestionAnswerData
    {
        /**
         * @var ApplicationSubmission
         */
        $applicationSubmission = ApplicationSubmission::find($request->application_submission_id);
        if ($applicationSubmission->state === ApplicationSubmissionState::Cancelled) {
            abort(403, 'Application was cancelled.');
        }

        $applicationQuestionAnswer = new ApplicationQuestionAnswer;
        $applicationQuestionAnswer->application_question_id = $request->application_question_id;
        $applicationQuestionAnswer->application_submission_id = $request->application_submission_id;
        $applicationQuestionAnswer->answer = $request->answer;
        $applicationQuestionAnswer->attachments = $request->attachments;
        $applicationQuestionAnswer->save();

        (new ApplicationActivityRepository)->questionAnswerCreated($applicationQuestionAnswer);

        return ApplicationQuestionAnswerData::from($applicationQuestionAnswer)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationQuestionAnswerRequest $request, ApplicationQuestionAnswer $applicationQuestionAnswer): ApplicationQuestionAnswerData
    {
        if (! $request->application_question_id instanceof Optional) {
            $applicationQuestionAnswer->application_question_id = $request->application_question_id;
        }

        if (! $request->application_submission_id instanceof Optional) {
            $applicationQuestionAnswer->application_submission_id = $request->application_submission_id;
        }

        if (! $request->answer instanceof Optional) {
            $applicationQuestionAnswer->answer = $request->answer;
        }

        if (! $request->attachments instanceof Optional) {
            $applicationQuestionAnswer->attachments = $request->attachments;
        }

        if ($applicationQuestionAnswer->isDirty()) {
            $applicationQuestionAnswer->save();
        }

        return ApplicationQuestionAnswerData::from($applicationQuestionAnswer)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteApplicationQuestionAnswerRequest $request, ApplicationQuestionAnswer $applicationQuestionAnswer): bool
    {
        return $applicationQuestionAnswer->delete() ?? false;
    }
}
