<?php

namespace App\Http\Controllers;

use App\Data\ServerContentData;
use App\Http\Requests\ServerContent\StoreRequest;
use App\Http\Requests\ServerContent\UpdateRequest;
use App\Models\ServerContent;
use App\Models\ServerContentMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ServerContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ServerContentData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('serverContent.read')) {
            abort(403);
        }
        $serverContent = QueryBuilder::for(ServerContent::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('is_recommended'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('id'),
            ])
            ->getOrPaginate();

        return ServerContentData::collect($serverContent, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ServerContentData
    {
        return ServerContentData::from(ServerContent::create($request->validated()))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ServerContent $serverContent): ServerContentData
    {
        $serverContent->update($request->validated());

        return ServerContentData::from($serverContent->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServerContent $serverContent): bool
    {
        if (! request()->user()?->can('serverContent.delete')) {
            abort(403);
        }

        return $serverContent->delete() ?? false;
    }

    public function resend(): bool
    {
        if (! request()->user()?->can('serverContent.resend')) {
            abort(403);
        }
        $data = request()->validate([
            'channel_id' => ['required', 'string'],
        ]);

        $messages = ServerContentMessage::where('server_id', config('services.discord.server_id'))->first();
        $notRecommended = ServerContent::notRecommended()->active()->get();
        $recommended = ServerContent::recommended()->active()->get();

        if (! $messages) {
            abort(400, 'No messages available');
        }

        $channelId = $data['channel_id'];

        Http::discordBot()->post('/channels/'.$channelId.'/messages', [
            'content' => $messages->heading,
        ]);

        if ($notRecommended->count()) {
            $notRecommendedMessages = $this->getMessages($messages->not_recommended, $notRecommended);
            foreach ($notRecommendedMessages as $notRecommendedMessage) {
                Http::discordBot()->post('/channels/'.$channelId.'/messages', [
                    'content' => $notRecommendedMessage,
                    'flags' => 4,
                ]);
            }
        }

        if ($recommended->count()) {
            $recommendedMessages = $this->getMessages($messages->recommended, $recommended);
            foreach ($recommendedMessages as $recommendedMessage) {
                Http::discordBot()->post('/channels/'.$channelId.'/messages', [
                    'content' => $recommendedMessage,
                    'flags' => 4,
                ]);
            }
        }

        return true;
    }

    /**
     * @param  Collection<int, ServerContent>  $serverContents
     * @return array<string>
     */
    private function getMessages(string $message, Collection $serverContents): array
    {
        $mapFunction = function ($data) {
            return '- ['.$data['name'].']('.$data['url'].")\n  - ".$data['description'];
        };

        $messages = [
            $message,
            ...array_map($mapFunction, $serverContents->toArray()),
        ];
        $chunkedMessages = [''];
        $key = 0;
        foreach ($messages as $message) {
            if (strlen($chunkedMessages[$key]) + strlen($message) + 2 > 2000) {
                $key++;
                $chunkedMessages[$key] = '';
            }

            $chunkedMessages[$key] .= $message."\n";
        }

        return $chunkedMessages;
    }
}
