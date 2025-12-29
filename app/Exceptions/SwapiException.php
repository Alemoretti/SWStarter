<?php

namespace App\Exceptions;

use Exception;

class SwapiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
