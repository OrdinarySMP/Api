<?php

namespace App\Data\Requests;

use App\Models\User;
use App\Rules\DiscordMessageRule;
use App\Rules\EmojiRule;
use App\Rules\RoleRule;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateReactionRoleRequest extends Data
{
    public function __construct(
        public readonly Optional|string $message_link,
        public readonly Optional|string $emoji,
        public readonly Optional|string $role_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('reactionRole.update');
    }

    /**
     * @return array<string, array<string|DiscordMessageRule|EmojiRule|RoleRule>>
     */
    public static function rules(): array
    {
        return [
            'message_link' => ['required', 'string', new DiscordMessageRule],
            'emoji' => ['required', 'string', new EmojiRule],
            'role_id' => ['required', 'string', new RoleRule],
        ];
    }
}
