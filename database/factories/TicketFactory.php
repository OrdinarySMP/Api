<?php

namespace Database\Factories;

use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\TicketButton;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_button_id' => TicketButton::factory(),
            'channel_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'state' => fake()->randomElement(TicketState::cases()),
            'created_by_discord_user_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'closed_by_discord_user_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'closed_reason' => fake()->optional()->sentence(1),
        ];
    }
}
