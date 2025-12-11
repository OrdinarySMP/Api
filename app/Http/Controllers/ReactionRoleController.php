<?php

namespace App\Http\Controllers;

use App\Data\ReactionRoleData;
use App\Data\Requests\CreateReactionRoleRequest;
use App\Data\Requests\DeleteReactionRoleRequest;
use App\Data\Requests\ReadReactionRoleRequest;
use App\Data\Requests\UpdateReactionRoleRequest;
use App\Models\ReactionRole;
use App\Rules\DiscordMessageRule;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;
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
    public function index(ReadReactionRoleRequest $request): PaginatedDataCollection|DataCollection
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

        if (request()->has('full')) {
            return ReactionRoleData::collect($reactionRoles, DataCollection::class)->wrap('data');
        }

        return ReactionRoleData::collect($reactionRoles, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateReactionRoleRequest $request): ReactionRoleData
    {
        [, $channelId, $messageId] = DiscordMessageRule::splitMessageLink($request->message_link);
        /**
         * @var string
         */
        $urlEmoji = str_replace('<', '', $request->emoji);
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

        $reactionRole = new ReactionRole;
        $reactionRole->emoji = $request->emoji;
        $reactionRole->role_id = $request->role_id;
        $reactionRole->message_id = $messageId;
        $reactionRole->channel_id = $channelId;
        $reactionRole->save();

        return ReactionRoleData::from($reactionRole)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReactionRoleRequest $request, ReactionRole $reactionRole): ReactionRoleData
    {

        if (! $request->message_link instanceof Optional) {
            [, $channelId, $messageId] = DiscordMessageRule::splitMessageLink($request->message_link);
            $reactionRole->message_id = $messageId;
            $reactionRole->channel_id = $channelId;
        }

        if (! $request->emoji instanceof Optional) {
            $reactionRole->emoji = $request->emoji;
        }

        if (! $request->role_id instanceof Optional) {
            $reactionRole->role_id = $request->role_id;
        }

        if ($reactionRole->isDirty()) {
            $reactionRole->save();
        }

        return ReactionRoleData::from($reactionRole)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteReactionRoleRequest $request, ReactionRole $reactionRole): bool
    {
        $urlEmoji = str_replace('<', '', $reactionRole->emoji);
        $urlEmoji = str_replace('>', '', $urlEmoji);
        $urlEmoji = urlencode($urlEmoji);
        Http::discordBot()->delete('/channels/'.$reactionRole->channel_id.'/messages/'.$reactionRole->message_id.'/reactions/'.$urlEmoji.'/@me');

        return $reactionRole->delete() ?? false;
    }
}
