<?php

declare(strict_types=1);

namespace App\Data\Discord\Component;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordActionRowData')]
class ActionRowData extends Data
{
    public function __construct(
        /** @var Collection<int, ButtonData>|Collection<int, StringCollectorData> */
        public readonly Collection $components,
        public readonly int $type = 1,
    ) {}
}
