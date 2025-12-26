<?php

namespace Database\Factories;

use App\Models\ReactionRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReactionRole>
 */
class ReactionRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'channel_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
            'emoji' => '<Test:100000000000000000>',
            'role_id' => (string) fake()->numberBetween(100000000000000000, 999999999999999999),
        ];
    }
}
