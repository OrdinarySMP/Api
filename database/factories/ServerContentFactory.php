<?php

namespace Database\Factories;

use App\Models\ServerContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServerContent>
 */
class ServerContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'url' => fake()->url(),
            'description' => fake()->sentence(2),
            'is_recommended' => fake()->boolean(),
            'is_active' => fake()->boolean(),
        ];
    }

    public function recommended(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_recommended' => true,
            ];
        });
    }

    public function notRecommended(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_recommended' => false,
            ];
        });
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }
}
