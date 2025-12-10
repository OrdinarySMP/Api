<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreateServerContentRequest;
use App\Data\Requests\DeleteServerContentRequest;
use App\Data\Requests\ReadServerContentRequest;
use App\Data\Requests\ResendServerContentRequest;
use App\Data\Requests\UpdateServerContentRequest;
use App\Data\ServerContentData;
use App\Models\ServerContent;
use App\Models\ServerContentMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Optional;
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
    public function index(ReadServerContentRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateServerContentRequest $request): ServerContentData
    {
        $serverContent = new ServerContent;
        $serverContent->name = $request->name;
        $serverContent->url = $request->url;
        $serverContent->description = $request->description;
        $serverContent->is_recommended = $request->is_recommended;
        $serverContent->is_active = $request->is_active;
        $serverContent->save();

        return ServerContentData::from($serverContent)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServerContentRequest $request, ServerContent $serverContent): ServerContentData
    {
        if (! $request->name instanceof Optional) {
            $serverContent->name = $request->name;
        }

        if (! $request->url instanceof Optional) {
            $serverContent->url = $request->url;
        }

        if (! $request->description instanceof Optional) {
            $serverContent->description = $request->description;
        }

        if (! $request->is_recommended instanceof Optional) {
            $serverContent->is_recommended = $request->is_recommended;
        }

        if (! $request->is_active instanceof Optional) {
            $serverContent->is_active = $request->is_active;
        }

        if ($serverContent->isDirty()) {
            $serverContent->save();
        }

        return ServerContentData::from($serverContent)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteServerContentRequest $request, ServerContent $serverContent): bool
    {
        if (! request()->user()?->can('serverContent.delete')) {
            abort(403);
        }

        return $serverContent->delete() ?? false;
    }

    public function resend(ResendServerContentRequest $request): bool
    {
        $messages = ServerContentMessage::where('server_id', config('services.discord.server_id'))->first();
        $notRecommended = ServerContent::notRecommended()->active()->get();
        $recommended = ServerContent::recommended()->active()->get();

        if (! $messages) {
            abort(400, 'No messages available');
        }

        Http::discordBot()->post('/channels/'.$request->channel_id.'/messages', [
            'content' => $messages->heading,
        ]);

        if ($notRecommended->count()) {
            $notRecommendedMessages = $this->getMessages($messages->not_recommended, $notRecommended);
            foreach ($notRecommendedMessages as $notRecommendedMessage) {
                Http::discordBot()->post('/channels/'.$request->channel_id.'/messages', [
                    'content' => $notRecommendedMessage,
                    'flags' => 4,
                ]);
            }
        }

        if ($recommended->count()) {
            $recommendedMessages = $this->getMessages($messages->recommended, $recommended);
            foreach ($recommendedMessages as $recommendedMessage) {
                Http::discordBot()->post('/channels/'.$request->channel_id.'/messages', [
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
