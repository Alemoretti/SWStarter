<?php

namespace App\DTOs;

class SearchResultDto
{
    public function __construct(
        public readonly array $results,
        public readonly int $count,
        public readonly string $type,
    ) {}
}
