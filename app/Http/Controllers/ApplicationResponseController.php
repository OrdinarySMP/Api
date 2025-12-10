<?php

namespace App\Http\Controllers;

use App\Data\ApplicationResponseData;
use App\Data\Requests\CreateApplicationResponseRequest;
use App\Data\Requests\DeleteApplicationResponseRequest;
use App\Data\Requests\ReadApplicationResponseRequest;
use App\Data\Requests\UpdateApplicationResponseRequest;
use App\Models\ApplicationResponse;
use Spatie\LaravelData\Optional;
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
    public function index(ReadApplicationResponseRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateApplicationResponseRequest $request): ApplicationResponseData
    {
        $applicationResponse = new ApplicationResponse;
        $applicationResponse->type = $request->type;
        $applicationResponse->name = $request->name;
        $applicationResponse->response = $request->response;
        $applicationResponse->application_id = $request->application_id;
        $applicationResponse->save();

        return ApplicationResponseData::from($applicationResponse)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationResponseRequest $request, ApplicationResponse $applicationResponse): ApplicationResponseData
    {
        if (! $request->type instanceof Optional) {
            $applicationResponse->type = $request->type;
        }

        if (! $request->name instanceof Optional) {
            $applicationResponse->name = $request->name;
        }

        if (! $request->response instanceof Optional) {
            $applicationResponse->response = $request->response;
        }

        if (! $request->application_id instanceof Optional) {
            $applicationResponse->application_id = $request->application_id;
        }

        if ($applicationResponse->isDirty()) {
            $applicationResponse->save();
        }

        return ApplicationResponseData::from($applicationResponse->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteApplicationResponseRequest $request, ApplicationResponse $applicationResponse): bool
    {
        return $applicationResponse->delete() ?? false;
    }
}
