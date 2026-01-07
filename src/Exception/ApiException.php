<?php

namespace App\Exception;

use RuntimeException;

abstract class ApiException extends RuntimeException
{
    public function __construct(
        private string $errorCode,
        string $message,
        private int $statusCode
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
