<?php

namespace App\Http\Controllers;

use App\Data\ApplicationQuestionData;
use App\Data\Requests\CreateApplicationQuestionRequest;
use App\Data\Requests\DeleteApplicationQuestionRequest;
use App\Data\Requests\ReadApplicationQuestionRequest;
use App\Data\Requests\UpdateApplicationQuestionRequest;
use App\Models\ApplicationQuestion;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ApplicationQuestionData>
     */
    public function index(ReadApplicationQuestionRequest $request): PaginatedDataCollection
    {
        $applicationQuestion = QueryBuilder::for(ApplicationQuestion::class)
            ->defaultSort('order')
            ->allowedSorts('order')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('application_id'),
            ])
            ->getOrPaginate();

        return ApplicationQuestionData::collect($applicationQuestion, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateApplicationQuestionRequest $request): ApplicationQuestionData
    {
        $applicationQuestion = new ApplicationQuestion;
        $applicationQuestion->question = $request->question;
        $applicationQuestion->order = $request->order;
        $applicationQuestion->is_active = $request->is_active;
        $applicationQuestion->application_id = $request->application_id;
        $applicationQuestion->save();

        return ApplicationQuestionData::from($applicationQuestion)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationQuestionRequest $request, ApplicationQuestion $applicationQuestion): ApplicationQuestionData
    {
        if (! $request->question instanceof Optional) {
            $applicationQuestion->question = $request->question;
        }

        if (! $request->order instanceof Optional) {
            $applicationQuestion->order = $request->order;
        }

        if (! $request->is_active instanceof Optional) {
            $applicationQuestion->is_active = $request->is_active;
        }

        if (! $request->application_id instanceof Optional) {
            $applicationQuestion->application_id = $request->application_id;
        }

        if ($applicationQuestion->isDirty()) {
            $applicationQuestion->save();
        }

        return ApplicationQuestionData::from($applicationQuestion)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteApplicationQuestionRequest $request, ApplicationQuestion $applicationQuestion): bool
    {
        if (! request()->user()?->can('applicationQuestion.delete')) {
            abort(403);
        }

        return $applicationQuestion->delete() ?? false;
    }
}
