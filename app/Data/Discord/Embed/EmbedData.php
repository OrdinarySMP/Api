<?php

declare(strict_types=1);

namespace App\Data\Discord\Embed;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordEmbedData')]
class EmbedData extends Data
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $color = null,
        /** @var ?Collection<int, FieldsData> $fields */
        public readonly ?Collection $fields = null,
        public readonly ?string $timestamp = null,
        public readonly ?ThumbnailData $thumbnail = null,
    ) {}
}
