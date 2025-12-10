<?php

namespace App\Data\Requests;

use App\Enums\DiscordButton;
use App\Models\TicketPanel;
use App\Models\TicketTeam;
use App\Models\User;
use App\Rules\EmojiRule;
use Illuminate\Container\Attributes\CurrentUser;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

#[MergeValidationRules]
class UpdateTicketButtonRequest extends Data
{
    public function __construct(
        #[Exists(TicketTeam::class, 'id')]
        public readonly Optional|int $ticket_team_id,
        #[Exists(TicketPanel::class, 'id')]
        public readonly Optional|int $ticket_panel_id,
        #[Max(50)]
        public readonly Optional|string $text,
        #[Max(7)]
        public readonly Optional|DiscordButton $color,
        #[Max(1000)]
        public readonly Optional|string $initial_message,
        public readonly Optional|string $emoji,
        #[Max(128)]
        public readonly Optional|string $naming_scheme,
        public readonly Optional|bool $disabled,
        /** @var ?array<array-key, string> */
        public readonly Optional|null|array $ticket_button_ping_role_ids,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('ticketButton.update');
    }

    /**
     * @return array<string, array<EmojiRule>>
     */
    public static function rules(): array
    {
        return [
            'emoji' => [new EmojiRule],
        ];
    }
}
