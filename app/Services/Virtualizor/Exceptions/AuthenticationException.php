<?php

namespace App\Services\Virtualizor\Exceptions;

class AuthenticationException extends VirtualizorApiException
{
    public function __construct(
        string $message = 'Authentication failed with Virtualizor server',
        ?array $apiError = null,
        int $code = 0
    ) {
        parent::__construct('authentication', $apiError, $message, $code);
    }
}
