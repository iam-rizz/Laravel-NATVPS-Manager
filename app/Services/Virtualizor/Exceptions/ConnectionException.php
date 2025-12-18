<?php

namespace App\Services\Virtualizor\Exceptions;

class ConnectionException extends VirtualizorApiException
{
    public function __construct(
        string $message = 'Failed to connect to Virtualizor server',
        ?array $apiError = null,
        int $code = 0
    ) {
        parent::__construct('connection', $apiError, $message, $code);
    }
}
