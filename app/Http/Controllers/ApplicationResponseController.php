<?php

namespace App\Http\Controllers;

use App\Data\ApplicationResponseData;
use App\Http\Requests\ApplicationResponse\StoreRequest;
use App\Http\Requests\ApplicationResponse\UpdateRequest;
use App\Models\ApplicationResponse;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ApplicationResponseData>
     */
    public function index(): PaginatedDataCollection
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

        return ApplicationResponseData::collect($applicationResponse, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationResponseData
    {
        return ApplicationResponseData::from(ApplicationResponse::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationResponse $applicationResponse): ApplicationResponseData
    {
        $applicationResponse->update($request->validated());

        return ApplicationResponseData::from($applicationResponse->refresh())->wrap('data');
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
