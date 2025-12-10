<?php

namespace App\Data\Requests;

use App\Models\User;
use App\Rules\DiscordMessageRule;
use App\Rules\EmojiRule;
use App\Rules\RoleRule;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Data;

class CreateReactionRoleRequest extends Data
{
    public function __construct(
        public readonly string $message_link,
        public readonly string $emoji,
        public readonly string $role_id,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('reactionRole.create');
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
