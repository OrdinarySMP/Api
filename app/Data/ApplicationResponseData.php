<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ApplicationResponseType;
use App\Models\ApplicationResponse;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApplicationResponseData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ApplicationResponseType $type,
        public readonly string $name,
        public readonly string $response,
        public readonly int $application_id,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}

    public static function fromApplicationResponse(ApplicationResponse $applicationResponse): self
    {
        return new self(
            $applicationResponse->id,
            $applicationResponse->type,
            $applicationResponse->name,
            $applicationResponse->response,
            $applicationResponse->application_id,
            $applicationResponse->created_at,
            $applicationResponse->updated_at,
        );
    }
}
