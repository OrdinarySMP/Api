<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateRuleRequest;
use App\Data\Requests\DeleteRuleRequest;
use App\Data\Requests\ReadRuleRequest;
use App\Data\Requests\UpdateRuleRequest;
use App\Data\RuleData;
use App\Models\Rule;
use Spatie\LaravelData\Optional;
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
    public function index(ReadRuleRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateRuleRequest $request): RuleData
    {
        $rule = new Rule;
        $rule->number = $request->number;
        $rule->name = $request->name;
        $rule->rule = $request->rule;
        $rule->save();

        return RuleData::from($rule)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRuleRequest $request, Rule $rule): RuleData
    {
        if (! $request->number instanceof Optional) {
            $rule->number = $request->number;
        }

        if (! $request->name instanceof Optional) {
            $rule->name = $request->name;
        }

        if (! $request->rule instanceof Optional) {
            $rule->rule = $request->rule;
        }

        if ($rule->isDirty()) {
            $rule->save();
        }

        return RuleData::from($rule)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteRuleRequest $request, Rule $rule): bool
    {
        return $rule->delete() ?? false;
    }
}
