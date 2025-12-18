<?php

namespace App\Services\Virtualizor\DTOs;

class ConnectionResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly ?array $details = null
    ) {}

    public static function success(?string $message = null, ?array $details = null): self
    {
        return new self(true, $message ?? 'Connection successful', $details);
    }

    public static function failure(string $message, ?array $details = null): self
    {
        return new self(false, $message, $details);
    }
}
