<?php

namespace App\Data\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class LoginRequest extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $password,
        public readonly Optional|bool $remember,
    ) {}
}
