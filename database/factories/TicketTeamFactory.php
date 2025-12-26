<?php

namespace Database\Factories;

use App\Models\TicketTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketTeam>
 */
class TicketTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
