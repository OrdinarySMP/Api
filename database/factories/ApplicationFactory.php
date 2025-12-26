<?php

namespace Database\Factories;

use App\Enums\DiscordButton;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
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

    public function withEmbed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'embed_title' => fake()->sentence(),
                'embed_description' => fake()->text(),
                'embed_color' => fake()->hexColor(),
                'embed_channel_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
                'embed_button_color' => fake()->randomElement(DiscordButton::cases()),
                'embed_button_text' => fake()->word(),
            ];
        });
    }
}
