<?php

declare(strict_types=1);

namespace App\Data\Discord\Component;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordStringCollectorData')]
class StringCollectorData extends Data
{
    public function __construct(
        public readonly string $custom_id,
        /** @var Collection<int, StringCollectorOptionData> $options */
        public readonly Collection $options,
        public readonly string $placeholder,
        public readonly int $min_values,
        public readonly int $max_values,
        public readonly int $type = 3,
    ) {}
}
