<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketTranscript;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketTranscript>
 */
class TicketTranscriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'discord_user_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'message_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'message' => fake()->paragraph(),
        ];
    }
}
