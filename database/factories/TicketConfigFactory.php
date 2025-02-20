<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketConfig>
 */
class TicketConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guild_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'category_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'transcript_channel_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
        ];
    }
}
