<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guild_id' => config('services.discord.server_id'),
            'name' => fake()->name(),
            'is_active' => fake()->boolean(),
            'log_channel' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'accept_message' => fake()->sentence(2),
            'deny_message' => fake()->sentence(2),
            'confirmation_message' => fake()->sentence(2),
            'completion_message' => fake()->sentence(2),
        ];
    }
}
