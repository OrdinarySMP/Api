<?php

declare(strict_types=1);

namespace App\Data\Discord\Embed;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordThumbnailData')]
class ThumbnailData extends Data
{
    public function __construct(
        public readonly string $url,
    ) {}
}
