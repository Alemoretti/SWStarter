<?php

namespace App\Exceptions;

class SwapiNotFoundException extends SwapiException
{
    public function __construct(string $resource, ?\Throwable $previous = null)
    {
        parent::__construct("{$resource} not found", 404, $previous);
    }
}