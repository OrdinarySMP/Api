<?php

namespace Database\Factories;

use App\Models\ApplicationQuestion;
use App\Models\ApplicationSubmission;
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
            'application_question_id' => ApplicationQuestion::factory(),
            'application_submission_id' => ApplicationSubmission::factory(),
            'answer' => fake()->sentence(2),
        ];
    }
}
