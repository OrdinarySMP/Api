<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationQuestion\StoreRequest;
use App\Http\Requests\ApplicationQuestion\UpdateRequest;
use App\Http\Resources\ApplicationQuestionResource;
use App\Models\ApplicationQuestion;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
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

        return ApplicationQuestionResource::collection($applicationQuestion);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationQuestionResource
    {
        return new ApplicationQuestionResource(ApplicationQuestion::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationQuestion $applicationQuestion): ApplicationQuestionResource
    {
        $applicationQuestion->update($request->validated());

        return new ApplicationQuestionResource($applicationQuestion->refresh());
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
