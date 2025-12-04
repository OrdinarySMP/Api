<?php

namespace App\Repositories;

use App\Data\Discord\UserData;
use App\Models\TicketTranscript;

class TicketTranscriptRepository
{
    public function __construct(
        protected DiscordRepository $discordRepository,
    ) {}

    public function getUser(TicketTranscript $ticketTranscript): ?UserData
    {
        return $this->discordRepository->getUserById($ticketTranscript->discord_user_id);
    }
}
