<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class ApplicationQuestionAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_question_id' => ApplicationQuestion::factory()->create(),
            'application_id' => Application::factory()->create(),
            'answer' => fake()->sentence(2),
        ];
    }
}
