<?php

declare(strict_types=1);

namespace App\Data\Discord\Component;

use App\Models\ApplicationResponse;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordStringCollectorOptionData')]
class StringCollectorOptionData extends Data
{
    public function __construct(
        public readonly string $label,
        public readonly string $value,
        public readonly string $description,
    ) {}

    public static function fromApplicationResponse(ApplicationResponse $applicationResponse): self
    {
        return new self(
            $applicationResponse->name,
            "{$applicationResponse->id}",
            Str::limit($applicationResponse->response, 90),
        );
    }
}
