<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationQuestion;
use App\Models\ApplicationQuestionAnswer;
use App\Models\ApplicationResponse;
use App\Models\ApplicationRole;
use App\Models\ApplicationSubmission;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $applications = Application::factory(5)
            ->has(
                ApplicationQuestion::factory(5)
            )
            ->has(
                ApplicationSubmission::factory(5)
            )
            ->has(
                ApplicationRole::factory(10)
            )
            ->has(
                ApplicationResponse::factory(10)
            )
            ->create();

        $applications->each(function ($application) {
            $application->applicationSubmissions->each(function ($submission) use ($application) {
                $application->applicationQuestions->each(function ($question) use ($submission) {
                    ApplicationQuestionAnswer::factory()->create([
                        'application_question_id' => $question->id,
                        'application_submission_id' => $submission->id,
                    ]);
                });
            });
        });
    }
}
