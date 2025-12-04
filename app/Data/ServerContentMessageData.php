<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ServerContentMessage;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ServerContentMessageData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $heading,
        public readonly string $not_recommended,
        public readonly string $recommended,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromFaq(ServerContentMessage $serverContentMessage): self
    {
        return new self(
            $serverContentMessage->id,
            $serverContentMessage->heading,
            $serverContentMessage->not_recommended,
            $serverContentMessage->recommended,
            $serverContentMessage->created_at,
            $serverContentMessage->updated_at,
        );
    }
}
