<?php

namespace App\Http\Controllers;

use App\Http\Requests\Application\StoreRequest;
use App\Http\Requests\Application\UpdateRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        if (! request()->user()?->can('application.read')) {
            abort(403);
        }
        $applications = QueryBuilder::for(Application::class)
            ->allowedIncludes(['restrictedRoles'])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'name',
            ])
            ->getOrPaginate();

        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationResource
    {
        abort(503);

        // return new ApplicationResource(Application::create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Application $application): ApplicationResource
    {
        abort(503);
        // $application->update($request->validated());

        // return new ApplicationResource($application->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application): bool
    {
        if (! request()->user()?->can('application.delete')) {
            abort(403);
        }
        abort(503);
        // return $application->delete() ?? false;
    }
}
