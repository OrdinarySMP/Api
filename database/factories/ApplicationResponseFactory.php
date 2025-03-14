<?php

namespace Database\Factories;

use App\Enums\ApplicationResponseType;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class ApplicationResponseFactory extends Factory
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
            'type' => fake()->randomElement(ApplicationResponseType::cases()),
            'name' => fake()->sentence(1),
            'response' => fake()->sentence(3),
        ];
    }
}
