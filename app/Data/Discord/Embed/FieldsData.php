<?php

declare(strict_types=1);

namespace App\Data\Discord\Embed;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('DiscordFieldsData')]
class FieldsData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
        public readonly ?bool $inline = null,
    ) {}
}
