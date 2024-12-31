<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationResponse\StoreRequest;
use App\Http\Requests\ApplicationResponse\UpdateRequest;
use App\Http\Resources\ApplicationResponseResource;
use App\Models\ApplicationResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('applicationResponse.read')) {
            abort(403);
        }
        $applicationResponse = QueryBuilder::for(ApplicationResponse::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('application_id'),
            ])
            ->getOrPaginate();

        return ApplicationResponseResource::collection($applicationResponse);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationResponseResource
    {
        return new ApplicationResponseResource(ApplicationResponse::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationResponse $applicationResponse): ApplicationResponseResource
    {
        $applicationResponse->update($request->validated());

        return new ApplicationResponseResource($applicationResponse->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationResponse $applicationResponse): bool
    {
        if (! request()->user()?->can('applicationResponse.delete')) {
            abort(403);
        }

        return $applicationResponse->delete() ?? false;
    }
}
