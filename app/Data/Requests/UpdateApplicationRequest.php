<?php

namespace App\Data\Requests;

use App\Enums\DiscordButton;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateApplicationRequest extends Data
{
    public function __construct(
        public readonly Optional|string $name,
        public readonly Optional|bool $is_active,
        public readonly Optional|string $log_channel,
        public readonly Optional|string $accept_message,
        public readonly Optional|string $deny_message,
        public readonly Optional|string $confirmation_message,
        public readonly Optional|string $completion_message,
        public readonly Optional|null|string $activity_channel,
        /** @var array<string> */
        public readonly Optional|array $restricted_role_ids,
        /** @var array<string> */
        public readonly Optional|array $accepted_role_ids,
        /** @var array<string> */
        public readonly Optional|array $denied_role_ids,
        /** @var array<string> */
        public readonly Optional|array $ping_role_ids,
        /** @var array<string> */
        public readonly Optional|array $accept_removal_role_ids,
        /** @var array<string> */
        public readonly Optional|array $deny_removal_role_ids,
        /** @var array<string> */
        public readonly Optional|array $pending_role_ids,
        /** @var array<string> */
        public readonly Optional|array $required_role_ids,
        #[Max(20)]
        public readonly Optional|null|string $embed_channel_id,
        #[Max(100)]
        public readonly Optional|null|string $embed_title,
        #[Max(1000)]
        public readonly Optional|null|string $embed_description,
        #[Max(7)]
        public readonly Optional|null|string $embed_color,
        #[Max(50)]
        public readonly Optional|null|string $embed_button_text,
        #[Max(7)]
        public readonly Optional|null|DiscordButton $embed_button_color,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('application.update');
    }
}
