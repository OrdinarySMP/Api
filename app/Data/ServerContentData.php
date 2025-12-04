<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ServerContent;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ServerContentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $url,
        public readonly string $description,
        public readonly bool $is_recommended,
        public readonly bool $is_active,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromFaq(ServerContent $serverContent): self
    {
        return new self(
            $serverContent->id,
            $serverContent->name,
            $serverContent->url,
            $serverContent->description,
            $serverContent->is_recommended,
            $serverContent->is_active,
            $serverContent->created_at,
            $serverContent->updated_at,
        );
    }
}
