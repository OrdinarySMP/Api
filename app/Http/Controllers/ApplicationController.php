<?php

namespace App\Http\Controllers;

use App\Data\ApplicationData;
use App\Enums\ApplicationRoleType;
use App\Http\Requests\Application\StoreRequest;
use App\Http\Requests\Application\UpdateRequest;
use App\Models\Application;
use App\Models\ApplicationRole;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PaginatedDataCollection<array-key, ApplicationData>
     */
    public function index(): PaginatedDataCollection
    {
        if (! request()->user()?->can('application.read')) {
            abort(403);
        }
        $applications = QueryBuilder::for(Application::class)
            ->allowedIncludes([
                'applicationQuestions',
                'acceptedResponses',
                'deniedResponses',
                'restrictedRoles',
                'acceptedRoles',
                'deniedRoles',
                'pingRoles',
                'acceptRemovalRoles',
                'denyRemovalRoles',
                'pendingRoles',
                'requiredRoles',
            ])
            ->allowedSorts([
                'id',
                'name',
                'is_active',
            ])
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'name',
            ])
            ->getOrPaginate();

        return ApplicationData::collect($applications, PaginatedDataCollection::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): ApplicationData
    {
        $data = $request->validated();
        $data = [
            ...$data,
            'guild_id' => config('services.discord.server_id'),
        ];
        $application = Application::create($data);

        if (array_key_exists('restricted_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $restrictedRoleIds = $data['restricted_role_ids'];
            $restrictedRole = collect($restrictedRoleIds)->map(fn ($restrictedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $restrictedRoleId,
                'type' => ApplicationRoleType::Restricted,
            ]);
            ApplicationRole::insert($restrictedRole->toArray());
        }

        if (array_key_exists('accepted_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $acceptedRoleIds = $data['accepted_role_ids'];
            $acceptedRole = collect($acceptedRoleIds)->map(fn ($acceptedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $acceptedRoleId,
                'type' => ApplicationRoleType::Accepted,
            ]);
            ApplicationRole::insert($acceptedRole->toArray());
        }

        if (array_key_exists('denied_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $deniedRoleIds = $data['denied_role_ids'];
            $deniedRole = collect($deniedRoleIds)->map(fn ($deniedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $deniedRoleId,
                'type' => ApplicationRoleType::Denied,
            ]);
            ApplicationRole::insert($deniedRole->toArray());
        }

        if (array_key_exists('ping_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $pingRoleIds = $data['ping_role_ids'];
            $pingRole = collect($pingRoleIds)->map(fn ($pingRoleId) => [
                'application_id' => $application->id,
                'role_id' => $pingRoleId,
                'type' => ApplicationRoleType::Ping,
            ]);
            ApplicationRole::insert($pingRole->toArray());
        }

        if (array_key_exists('accept_removal_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $acceptRemovalRoleIds = $data['accept_removal_role_ids'];
            $acceptRemovalRole = collect($acceptRemovalRoleIds)->map(fn ($acceptRemovalRoleId) => [
                'application_id' => $application->id,
                'role_id' => $acceptRemovalRoleId,
                'type' => ApplicationRoleType::AcceptRemoval,
            ]);
            ApplicationRole::insert($acceptRemovalRole->toArray());
        }

        if (array_key_exists('deny_removal_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $denyRemovalRoleIds = $data['deny_removal_role_ids'];
            $denyRemovalRole = collect($denyRemovalRoleIds)->map(fn ($denyRemovalRoleId) => [
                'application_id' => $application->id,
                'role_id' => $denyRemovalRoleId,
                'type' => ApplicationRoleType::DenyRemoval,
            ]);
            ApplicationRole::insert($denyRemovalRole->toArray());
        }

        if (array_key_exists('pending_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $pendingRoleIds = $data['pending_role_ids'];
            $pendingRole = collect($pendingRoleIds)->map(fn ($pendingRoleId) => [
                'application_id' => $application->id,
                'role_id' => $pendingRoleId,
                'type' => ApplicationRoleType::Pending,
            ]);
            ApplicationRole::insert($pendingRole->toArray());
        }

        if (array_key_exists('required_role_ids', $data)) {
            /**
             * @var array<string>
             */
            $requiredRoleIds = $data['required_role_ids'];
            $requiredRole = collect($requiredRoleIds)->map(fn ($requiredRoleId) => [
                'application_id' => $application->id,
                'role_id' => $requiredRoleId,
                'type' => ApplicationRoleType::Required,
            ]);
            ApplicationRole::insert($requiredRole->toArray());
        }

        return ApplicationData::from($application)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Application $application): ApplicationData
    {
        $data = $request->validated();
        $application->update($data);

        if (array_key_exists('restricted_role_ids', $data)) {
            $application->restrictedRoles()->delete();
            /**
             * @var array<string>
             */
            $restrictedRoleIds = $data['restricted_role_ids'];
            $restrictedRole = collect($restrictedRoleIds)->map(fn ($restrictedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $restrictedRoleId,
                'type' => ApplicationRoleType::Restricted,
            ]);
            ApplicationRole::insert($restrictedRole->toArray());
        }

        if (array_key_exists('accepted_role_ids', $data)) {
            $application->acceptedRoles()->delete();
            /**
             * @var array<string>
             */
            $acceptedRoleIds = $data['accepted_role_ids'];
            $acceptedRole = collect($acceptedRoleIds)->map(fn ($acceptedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $acceptedRoleId,
                'type' => ApplicationRoleType::Accepted,
            ]);
            ApplicationRole::insert($acceptedRole->toArray());
        }

        if (array_key_exists('denied_role_ids', $data)) {
            $application->deniedRoles()->delete();
            /**
             * @var array<string>
             */
            $deniedRoleIds = $data['denied_role_ids'];
            $deniedRole = collect($deniedRoleIds)->map(fn ($deniedRoleId) => [
                'application_id' => $application->id,
                'role_id' => $deniedRoleId,
                'type' => ApplicationRoleType::Denied,
            ]);
            ApplicationRole::insert($deniedRole->toArray());
        }

        if (array_key_exists('ping_role_ids', $data)) {
            $application->pingRoles()->delete();
            /**
             * @var array<string>
             */
            $pingRoleIds = $data['ping_role_ids'];
            $pingRole = collect($pingRoleIds)->map(fn ($pingRoleId) => [
                'application_id' => $application->id,
                'role_id' => $pingRoleId,
                'type' => ApplicationRoleType::Ping,
            ]);
            ApplicationRole::insert($pingRole->toArray());
        }

        if (array_key_exists('accept_removal_role_ids', $data)) {
            $application->acceptRemovalRoles()->delete();
            /**
             * @var array<string>
             */
            $acceptRemovalRoleIds = $data['accept_removal_role_ids'];
            $acceptRemovalRole = collect($acceptRemovalRoleIds)->map(fn ($acceptRemovalRoleId) => [
                'application_id' => $application->id,
                'role_id' => $acceptRemovalRoleId,
                'type' => ApplicationRoleType::AcceptRemoval,
            ]);
            ApplicationRole::insert($acceptRemovalRole->toArray());
        }

        if (array_key_exists('deny_removal_role_ids', $data)) {
            $application->denyRemovalRoles()->delete();
            /**
             * @var array<string>
             */
            $denyRemovalRoleIds = $data['deny_removal_role_ids'];
            $denyRemovalRole = collect($denyRemovalRoleIds)->map(fn ($denyRemovalRoleId) => [
                'application_id' => $application->id,
                'role_id' => $denyRemovalRoleId,
                'type' => ApplicationRoleType::DenyRemoval,
            ]);
            ApplicationRole::insert($denyRemovalRole->toArray());
        }

        if (array_key_exists('pending_role_ids', $data)) {
            $application->pendingRoles()->delete();
            /**
             * @var array<string>
             */
            $pendingRoleIds = $data['pending_role_ids'];
            $pendingRole = collect($pendingRoleIds)->map(fn ($pendingRoleId) => [
                'application_id' => $application->id,
                'role_id' => $pendingRoleId,
                'type' => ApplicationRoleType::Pending,
            ]);
            ApplicationRole::insert($pendingRole->toArray());
        }

        if (array_key_exists('required_role_ids', $data)) {
            $application->requiredRoles()->delete();
            /**
             * @var array<string>
             */
            $requiredRoleIds = $data['required_role_ids'];
            $requiredRole = collect($requiredRoleIds)->map(fn ($requiredRoleId) => [
                'application_id' => $application->id,
                'role_id' => $requiredRoleId,
                'type' => ApplicationRoleType::Required,
            ]);
            ApplicationRole::insert($requiredRole->toArray());
        }

        return ApplicationData::from($application)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application): bool
    {
        if (! request()->user()?->can('application.delete')) {
            abort(403);
        }

        return $application->delete() ?? false;
    }

    public function sendButton(Application $application): bool
    {
        if (! $application->embed_title ||
            ! $application->embed_description ||
            ! $application->embed_color ||
            ! $application->embed_channel_id
        ) {
            return false;
        }
        $data = [
            'embeds' => [
                [
                    'title' => $application->embed_title,
                    'description' => $application->embed_description,
                    'color' => hexdec(str_replace('#', '', $application->embed_color)),
                ],
            ],
            'components' => [
                [
                    'type' => 1,
                    'components' => [[
                        'type' => 2, // button
                        'custom_id' => 'applicationSubmission-start-'.$application->id,
                        'style' => $application->embed_button_color,
                        'label' => $application->embed_button_text,
                    ]],
                ],
            ],
        ];
        $response = Http::discordBot()->post('/channels/'.$application->embed_channel_id.'/messages', $data);

        return $response->ok();
    }
}
