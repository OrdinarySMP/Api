<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationQuestionAnswer\StoreRequest;
use App\Http\Requests\ApplicationQuestionAnswer\UpdateRequest;
use App\Http\Resources\ApplicationQuestionAnswerResource;
use App\Models\ApplicationQuestionAnswer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationQuestionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
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

        return ApplicationQuestionAnswerResource::collection($applicationQuestionAnswer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationQuestionAnswerResource
    {
        return new ApplicationQuestionAnswerResource(ApplicationQuestionAnswer::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationQuestionAnswer $applicationQuestionAnswer): ApplicationQuestionAnswerResource
    {
        $applicationQuestionAnswer->update($request->validated());

        return new ApplicationQuestionAnswerResource($applicationQuestionAnswer->refresh());
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
