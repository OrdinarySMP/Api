<?php

namespace Database\Factories;

use App\Models\TicketPanel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketPanel>
 */
class TicketPanelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(3),
            'message' => fake()->sentence(3),
            'embed_color' => '#123123',
            'channel_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
        ];
    }
}
