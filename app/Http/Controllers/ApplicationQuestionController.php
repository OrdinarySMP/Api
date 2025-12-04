<?php

namespace App\Http\Controllers;

use App\Data\ApplicationQuestionData;
use App\Http\Requests\ApplicationQuestion\StoreRequest;
use App\Http\Requests\ApplicationQuestion\UpdateRequest;
use App\Models\ApplicationQuestion;
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
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('applicationQuestion.read')) {
            abort(403);
        }
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
    public function store(StoreRequest $request): ApplicationQuestionData
    {
        return ApplicationQuestionData::from(ApplicationQuestion::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationQuestion $applicationQuestion): ApplicationQuestionData
    {
        $applicationQuestion->update($request->validated());

        return ApplicationQuestionData::from($applicationQuestion->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationQuestion $applicationQuestion): bool
    {
        if (! request()->user()?->can('applicationQuestion.delete')) {
            abort(403);
        }

        return $applicationQuestion->delete() ?? false;
    }
}
