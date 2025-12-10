<?php

namespace App\Http\Controllers;

use App\Data\ApplicationData;
use App\Data\Requests\CreateApplicationRequest;
use App\Data\Requests\DeleteApplicationRequest;
use App\Data\Requests\ReadApplicationRequest;
use App\Data\Requests\SendApplicationButtonRequest;
use App\Data\Requests\UpdateApplicationRequest;
use App\Enums\ApplicationRoleType;
use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Optional;
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
    public function index(ReadApplicationRequest $request): PaginatedDataCollection
    {
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
    public function store(CreateApplicationRequest $request): ApplicationData
    {

        $application = new Application;
        $application->name = $request->name;
        $application->is_active = $request->is_active;
        $application->log_channel = $request->log_channel;
        $application->accept_message = $request->accept_message;
        $application->deny_message = $request->deny_message;
        $application->confirmation_message = $request->confirmation_message;
        $application->completion_message = $request->completion_message;
        $application->activity_channel = $request->activity_channel;
        $application->embed_channel_id = $request->embed_channel_id;
        $application->embed_title = $request->embed_title;
        $application->embed_description = $request->embed_description;
        $application->embed_color = $request->embed_color;
        $application->embed_button_text = $request->embed_button_text;
        $application->embed_button_color = $request->embed_button_color;
        $application->guild_id = config('services.discord.server_id');
        $application->save();

        $restrictedRoleIds = collect($request->restricted_role_ids)
            ->map(fn ($restrictedRoleId) => [
                'role_id' => $restrictedRoleId,
                'type' => ApplicationRoleType::Restricted,
            ]);
        if ($restrictedRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($restrictedRoleIds);
        }

        $acceptedRoleIds = collect($request->accepted_role_ids)
            ->map(fn ($acceptedRoleId) => [
                'role_id' => $acceptedRoleId,
                'type' => ApplicationRoleType::Accepted,
            ]);
        if ($acceptedRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($acceptedRoleIds);
        }

        $deniedRoleIds = collect($request->denied_role_ids)
            ->map(fn ($deniedRoleId) => [
                'role_id' => $deniedRoleId,
                'type' => ApplicationRoleType::Denied,
            ]);
        if ($deniedRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($deniedRoleIds);
        }

        $pingRoleIds = collect($request->ping_role_ids)
            ->map(fn ($pingRoleId) => [
                'role_id' => $pingRoleId,
                'type' => ApplicationRoleType::Ping,
            ]);
        if ($pingRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($pingRoleIds);
        }

        $acceptRemovalRoleIds = collect($request->accept_removal_role_ids)
            ->map(fn ($acceptRemovalRoleId) => [
                'role_id' => $acceptRemovalRoleId,
                'type' => ApplicationRoleType::AcceptRemoval,
            ]);
        if ($acceptRemovalRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($acceptRemovalRoleIds);
        }

        $denyRemovalRoleIds = collect($request->deny_removal_role_ids)
            ->map(fn ($denyRemovalRoleId) => [
                'role_id' => $denyRemovalRoleId,
                'type' => ApplicationRoleType::DenyRemoval,
            ]);
        if ($denyRemovalRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($denyRemovalRoleIds);
        }

        $pendingRoleIds = collect($request->pending_role_ids)
            ->map(fn ($pendingRoleId) => [
                'role_id' => $pendingRoleId,
                'type' => ApplicationRoleType::Pending,
            ]);
        if ($pendingRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($pendingRoleIds);
        }

        $requiredRoleIds = collect($request->required_role_ids)
            ->map(fn ($requiredRoleId) => [
                'role_id' => $requiredRoleId,
                'type' => ApplicationRoleType::Required,
            ]);
        if ($requiredRoleIds->isNotEmpty()) {
            $application->applicationRoles()->createMany($requiredRoleIds);
        }

        return ApplicationData::from($application)->wrap('data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application): ApplicationData
    {
        if (! $request->name instanceof Optional) {
            $application->name = $request->name;
        }

        if (! $request->is_active instanceof Optional) {
            $application->is_active = $request->is_active;
        }

        if (! $request->log_channel instanceof Optional) {
            $application->log_channel = $request->log_channel;
        }

        if (! $request->accept_message instanceof Optional) {
            $application->accept_message = $request->accept_message;
        }

        if (! $request->deny_message instanceof Optional) {
            $application->deny_message = $request->deny_message;
        }

        if (! $request->confirmation_message instanceof Optional) {
            $application->confirmation_message = $request->confirmation_message;
        }

        if (! $request->completion_message instanceof Optional) {
            $application->completion_message = $request->completion_message;
        }

        if (! $request->activity_channel instanceof Optional) {
            $application->activity_channel = $request->activity_channel;
        }

        if (! $request->embed_channel_id instanceof Optional) {
            $application->embed_channel_id = $request->embed_channel_id;
        }

        if (! $request->embed_title instanceof Optional) {
            $application->embed_title = $request->embed_title;
        }

        if (! $request->embed_description instanceof Optional) {
            $application->embed_description = $request->embed_description;
        }

        if (! $request->embed_color instanceof Optional) {
            $application->embed_color = $request->embed_color;
        }

        if (! $request->embed_button_text instanceof Optional) {
            $application->embed_button_text = $request->embed_button_text;
        }

        if (! $request->embed_button_color instanceof Optional) {
            $application->embed_button_color = $request->embed_button_color;
        }

        if ($application->isDirty()) {
            $application->save();
        }

        if (! $request->restricted_role_ids instanceof Optional) {
            $application->restrictedRoles()->delete();
            $restrictedRole = collect($request->restricted_role_ids)
                ->map(fn ($restrictedRoleId) => [
                    'role_id' => $restrictedRoleId,
                    'type' => ApplicationRoleType::Restricted,
                ]);
            $application->applicationRoles()->createMany($restrictedRole);
        }

        if (! $request->accepted_role_ids instanceof Optional) {
            $application->acceptedRoles()->delete();
            $acceptedRole = collect($request->accepted_role_ids)
                ->map(fn ($acceptedRoleId) => [
                    'role_id' => $acceptedRoleId,
                    'type' => ApplicationRoleType::Accepted,
                ]);
            $application->applicationRoles()->createMany($acceptedRole);
        }

        if (! $request->denied_role_ids instanceof Optional) {
            $application->deniedRoles()->delete();
            $deniedRole = collect($request->denied_role_ids)
                ->map(fn ($deniedRoleId) => [
                    'role_id' => $deniedRoleId,
                    'type' => ApplicationRoleType::Denied,
                ]);
            $application->applicationRoles()->createMany($deniedRole);
        }

        if (! $request->ping_role_ids instanceof Optional) {
            $application->pingRoles()->delete();
            $pingRole = collect($request->ping_role_ids)
                ->map(fn ($pingRoleId) => [
                    'role_id' => $pingRoleId,
                    'type' => ApplicationRoleType::Ping,
                ]);
            $application->applicationRoles()->createMany($pingRole);
        }

        if (! $request->accept_removal_role_ids instanceof Optional) {
            $application->acceptRemovalRoles()->delete();
            $acceptRemovalRole = collect($request->accept_removal_role_ids)
                ->map(fn ($acceptRemovalRoleId) => [
                    'role_id' => $acceptRemovalRoleId,
                    'type' => ApplicationRoleType::AcceptRemoval,
                ]);
            $application->applicationRoles()->createMany($acceptRemovalRole);
        }

        if (! $request->deny_removal_role_ids instanceof Optional) {
            $application->denyRemovalRoles()->delete();
            $denyRemovalRole = collect($request->deny_removal_role_ids)
                ->map(fn ($denyRemovalRoleId) => [
                    'role_id' => $denyRemovalRoleId,
                    'type' => ApplicationRoleType::DenyRemoval,
                ]);
            $application->applicationRoles()->createMany($denyRemovalRole);
        }

        if (! $request->pending_role_ids instanceof Optional) {
            $application->pendingRoles()->delete();
            $pendingRole = collect($request->pending_role_ids)
                ->map(fn ($pendingRoleId) => [
                    'role_id' => $pendingRoleId,
                    'type' => ApplicationRoleType::Pending,
                ]);
            $application->applicationRoles()->createMany($pendingRole);
        }

        if (! $request->required_role_ids instanceof Optional) {
            $application->requiredRoles()->delete();
            $requiredRole = collect($request->required_role_ids)
                ->map(fn ($requiredRoleId) => [
                    'role_id' => $requiredRoleId,
                    'type' => ApplicationRoleType::Required,
                ]);
            $application->applicationRoles()->createMany($requiredRole);
        }

        return ApplicationData::from($application)->wrap('data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteApplicationRequest $request, Application $application): bool
    {
        if (! request()->user()?->can('application.delete')) {
            abort(403);
        }

        return $application->delete() ?? false;
    }

    public function sendButton(SendApplicationButtonRequest $request, Application $application): bool
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
