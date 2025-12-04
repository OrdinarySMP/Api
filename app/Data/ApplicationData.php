<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\DiscordButton;
use App\Models\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $guild_id,
        public readonly string $name,
        public readonly bool $is_active,
        public readonly string $log_channel,
        public readonly string $accept_message,
        public readonly string $deny_message,
        public readonly string $confirmation_message,
        public readonly string $completion_message,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $restricted_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $restricted_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $accepted_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $accepted_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $denied_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $denied_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $ping_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $ping_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $accept_removal_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $accept_removal_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $deny_removal_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $deny_removal_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $pending_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $pending_role_ids,

        /** @var Collection<array-key, ApplicationRoleData> */
        #[LiteralTypeScriptType('ApplicationRoleData[]')]
        public readonly Collection $required_roles,
        /** @var Collection<array-key, string> */
        #[LiteralTypeScriptType('string[]')]
        public readonly Collection $required_role_ids,

        public readonly ?string $activity_channel,
        public readonly ?string $embed_title,
        public readonly ?string $embed_description,
        public readonly ?string $embed_color,
        public readonly ?string $embed_channel_id,
        public readonly ?string $embed_button_text,
        public readonly ?DiscordButton $embed_button_color
    ) {}

    public static function fromApplication(Application $application): self
    {
        return new self(
            $application->id,
            $application->guild_id,
            $application->name,
            $application->is_active,
            $application->log_channel,
            $application->accept_message,
            $application->deny_message,
            $application->confirmation_message,
            $application->completion_message,
            $application->created_at,
            $application->updated_at,
            ApplicationRoleData::collect($application->restrictedRoles),
            ApplicationRoleData::collect($application->restrictedRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->acceptedRoles),
            ApplicationRoleData::collect($application->acceptedRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->deniedRoles),
            ApplicationRoleData::collect($application->deniedRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->pingRoles),
            ApplicationRoleData::collect($application->pingRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->acceptRemovalRoles),
            ApplicationRoleData::collect($application->acceptRemovalRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->denyRemovalRoles),
            ApplicationRoleData::collect($application->denyRemovalRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->pendingRoles),
            ApplicationRoleData::collect($application->pendingRoles)->pluck('role_id'),
            ApplicationRoleData::collect($application->requiredRoles),
            ApplicationRoleData::collect($application->requiredRoles)->pluck('role_id'),
            $application->activity_channel,
            $application->embed_title,
            $application->embed_description,
            $application->embed_color,
            $application->embed_channel_id,
            $application->embed_button_text,
            $application->embed_button_color,
        );
    }
}
