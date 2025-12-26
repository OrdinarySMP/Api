<?php

namespace Database\Factories;

use App\Enums\DiscordButton;
use App\Models\TicketButton;
use App\Models\TicketPanel;
use App\Models\TicketTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketButton>
 */
class TicketButtonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_team_id' => TicketTeam::factory(),
            'ticket_panel_id' => TicketPanel::factory(),
            'text' => fake()->word(2),
            'color' => fake()->randomElement(DiscordButton::cases()),
            'initial_message' => fake()->sentence(2),
            'emoji' => '<Test:100000000000000000>',
            'naming_scheme' => '%id%-Test',
        ];
    }
}
