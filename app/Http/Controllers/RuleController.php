<?php

namespace App\Http\Controllers;

use App\Data\RuleData;
use App\Http\Requests\Rule\StoreRequest;
use App\Http\Requests\Rule\UpdateRequest;
use App\Models\Rule;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, RuleData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('rule.read')) {
            abort(403);
        }
        $rules = QueryBuilder::for(Rule::class)
            ->defaultSort('number')
            ->allowedSorts('number')
            ->allowedFilters([
                'name',
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        return RuleData::collect($rules, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RuleData
    {
        return RuleData::from(Rule::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Rule $rule): RuleData
    {
        $rule->update($request->validated());

        return RuleData::from($rule->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rule $rule): bool
    {
        if (! request()->user()?->can('rule.delete')) {
            abort(403);
        }

        return $rule->delete() ?? false;
    }
}
