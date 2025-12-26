<?php

namespace App\DTOs;

class StatisticsDto
{
    public function __construct(
        public readonly array $topQueries,
        public readonly ?float $avgResponseTime,
        public readonly ?int $popularHour,
    ) {}
}
