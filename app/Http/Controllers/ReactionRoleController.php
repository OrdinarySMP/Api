<?php

namespace App\Http\Controllers;

use App\Data\ReactionRoleData;
use App\Http\Requests\ReactionRole\StoreRequest;
use App\Http\Requests\ReactionRole\UpdateRequest;
use App\Models\ReactionRole;
use App\Rules\DiscordMessageRule;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReactionRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ReactionRoleData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('reactionRole.read')) {
            abort(403);
        }
        $reactionRoles = QueryBuilder::for(ReactionRole::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('emoji'),
                AllowedFilter::exact('message_id'),
                AllowedFilter::exact('channel_id'),
            ])
            ->getOrPaginate();

        return ReactionRoleData::collect($reactionRoles, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ReactionRoleData
    {
        [, $channelId, $messageId] = DiscordMessageRule::splitMessageLink($request->validated('message_link'));
        /**
         * @var string
         */
        $urlEmoji = str_replace('<', '', $request->validated('emoji'));
        $urlEmoji = str_replace('>', '', $urlEmoji);
        $urlEmoji = urlencode($urlEmoji);
        $response = Http::discordBot()->put('/channels/'.$channelId.'/messages/'.$messageId.'/reactions/'.$urlEmoji.'/@me');
        if (! $response->successful()) {
            response([
                'errors' => [
                    'reaction' => ['Failed to create reaction.'],
                ],
            ], 400);
        }

        return ReactionRoleData::from(ReactionRole::create([
            ...$request->validated(),
            'message_id' => $messageId,
            'channel_id' => $channelId,
        ]))->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ReactionRole $reactionRole): ReactionRoleData
    {
        [, $channelId, $messageId] = DiscordMessageRule::splitMessageLink($request->validated('message_link'));
        $reactionRole->update([
            ...$request->validated(),
            'message_id' => $messageId,
            'channel_id' => $channelId,
        ]);

        return ReactionRoleData::from($reactionRole->refresh())->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReactionRole $reactionRole): bool
    {
        if (! request()->user()?->can('reactionRole.delete')) {
            abort(403);
        }
        $urlEmoji = str_replace('<', '', $reactionRole->emoji);
        $urlEmoji = str_replace('>', '', $urlEmoji);
        $urlEmoji = urlencode($urlEmoji);
        Http::discordBot()->delete('/channels/'.$reactionRole->channel_id.'/messages/'.$reactionRole->message_id.'/reactions/'.$urlEmoji.'/@me');

        return $reactionRole->delete() ?? false;
    }
}
