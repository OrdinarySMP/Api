<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\TicketConfig;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketConfigData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $guild_id,
        public readonly string $category_id,
        public readonly string $transcript_channel_id,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromTicketConfig(TicketConfig $ticketConfig): self
    {
        return new self(
            $ticketConfig->id,
            $ticketConfig->guild_id,
            $ticketConfig->category_id,
            $ticketConfig->transcript_channel_id,
            $ticketConfig->created_at,
            $ticketConfig->updated_at,
        );
    }
}
