<?php

namespace App\Services\Virtualizor\Exceptions;

use Exception;

class VirtualizorApiException extends Exception
{
    public function __construct(
        public readonly string $action,
        public readonly ?array $apiError = null,
        string $message = '',
        int $code = 0
    ) {
        parent::__construct($message ?: "API error during {$action}", $code);
    }
}
