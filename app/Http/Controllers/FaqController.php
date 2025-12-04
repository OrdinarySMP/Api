<?php

namespace App\Http\Controllers;

use App\Data\FaqData;
use App\Http\Requests\Faq\StoreRequest;
use App\Http\Requests\Faq\UpdateRequest;
use App\Models\Faq;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, FaqData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('faq.read')) {
            abort(403);
        }
        $faqs = QueryBuilder::for(Faq::class)
            ->allowedFilters([
                'question',
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        return FaqData::collect($faqs, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): FaqData
    {
        return FaqData::from(Faq::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Faq $faq): FaqData
    {
        $faq->update($request->validated());

        return FaqData::from($faq)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq): bool
    {
        if (! request()->user()?->can('faq.delete')) {
            abort(403);
        }

        return $faq->delete() ?? false;
    }
}
