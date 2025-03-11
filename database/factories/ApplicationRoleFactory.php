<?php

namespace Database\Factories;

use App\Enums\ApplicationRoleType;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationRole>
 */
class ApplicationRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'role_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'type' => fake()->randomElement(ApplicationRoleType::cases()),
        ];
    }
}
