<?php

namespace App\Http\Controllers;

use App\Data\FaqData;
use App\Data\Requests\CreateFaqRequest;
use App\Data\Requests\DeleteFaqRequest;
use App\Data\Requests\ReadFaqRequest;
use App\Data\Requests\UpdateFaqRequest;
use App\Models\Faq;
use Spatie\LaravelData\Optional;
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
    public function index(ReadFaqRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateFaqRequest $request): FaqData
    {
        $faq = new Faq;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return FaqData::from($faq)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq): FaqData
    {
        if (! $request->question instanceof Optional) {
            $faq->question = $request->question;
        }

        if (! $request->answer instanceof Optional) {
            $faq->answer = $request->answer;
        }

        if ($faq->isDirty()) {
            $faq->save();
        }

        return FaqData::from($faq)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteFaqRequest $request, Faq $faq): bool
    {
        return $faq->delete() ?? false;
    }
}
