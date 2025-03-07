<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationSubmission\StoreRequest;
use App\Http\Requests\ApplicationSubmission\UpdateRequest;
use App\Models\ApplicationSubmission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection<int, ApplicationSubmission>|LengthAwarePaginator<ApplicationSubmission>
     */
    public function index(): Collection|LengthAwarePaginator
    {
        return QueryBuilder::for(ApplicationSubmission::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('discord_id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('application_id'),
            ])
            ->getOrPaginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationSubmission
    {
        return ApplicationSubmission::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ApplicationSubmission $applicationSubmission): ApplicationSubmission
    {
        $applicationSubmission->update($request->validated());

        return $applicationSubmission->refresh();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationSubmission $applicationSubmission): bool
    {
        return $applicationSubmission->delete() ?? false;
    }
}
