<?php

namespace Database\Factories;

use App\Enums\ApplicationState;
use App\Models\ApplicationResponse;
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
            'discord_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'submitted_at' => fake()->dateTimeBetween('-2 years', '+2 years')->format('Y-m-d H:i:s'),
            'application_response_id' => ApplicationResponse::factory()->create(),
            'state' => fake()->randomElement(ApplicationState::cases()),
            'custom_response' => fake()->optional()->sentence(2),
            'message_link' => fake()->url(),
            'handled_by' => fake()->optional()->numberBetween(100000000000000000, 999999999999999999),
        ];
    }
}
