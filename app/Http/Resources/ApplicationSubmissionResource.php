<?php

namespace App\Http\Resources;

use App\Repositories\DiscordRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>|\Illuminate\Contracts\Support\Arrayable<int, mixed>|\JsonSerializable
     */
    public function toArray(Request $request): array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    {
        $discordRepository = new DiscordRepository;
        /** @var \App\Models\ApplicationSubmission $applicationSubmission */
        $applicationSubmission = $this->resource;
        $member = null;
        try {
            $member = $discordRepository->getGuildMemberById($applicationSubmission->discord_id);
        } catch (\Error $e) {
            $member = null;
        }

        return [
            ...$applicationSubmission->toArray(),
            'member' => $member,
        ];
    }
}
