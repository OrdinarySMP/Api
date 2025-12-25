<?php

namespace App\Data\Requests;

use App\Enums\DiscordButton;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CreateApplicationRequest extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly bool $is_active,
        public readonly string $log_channel,
        public readonly string $accept_message,
        public readonly string $deny_message,
        public readonly string $confirmation_message,
        public readonly string $completion_message,
        public readonly ?string $activity_channel,
        /** @var ?array<string> */
        public readonly ?array $restricted_role_ids,
        /** @var ?array<string> */
        public readonly ?array $accepted_role_ids,
        /** @var ?array<string> */
        public readonly ?array $denied_role_ids,
        /** @var ?array<string> */
        public readonly ?array $ping_role_ids,
        /** @var ?array<string> */
        public readonly ?array $accept_removal_role_ids,
        /** @var ?array<string> */
        public readonly ?array $deny_removal_role_ids,
        /** @var ?array<string> */
        public readonly ?array $pending_role_ids,
        /** @var ?array<string> */
        public readonly ?array $required_role_ids,
        #[Max(20)]
        public readonly ?string $embed_channel_id,
        #[Max(100)]
        public readonly ?string $embed_title,
        #[Max(1000)]
        public readonly ?string $embed_description,
        #[Max(7)]
        public readonly ?string $embed_color,
        #[Max(50)]
        public readonly ?string $embed_button_text,
        #[Max(7)]
        public readonly ?DiscordButton $embed_button_color,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('application.create');
    }
}
