<?php

namespace App\Services\Virtualizor\DTOs;

class ActionResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly ?array $data = null
    ) {}

    public static function success(?string $message = null, ?array $data = null): self
    {
        return new self(true, $message ?? 'Action completed successfully', $data);
    }

    public static function failure(string $message, ?array $data = null): self
    {
        return new self(false, $message, $data);
    }
}
