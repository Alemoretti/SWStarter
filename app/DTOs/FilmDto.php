<?php

namespace App\DTOs;

class FilmDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $openingCrawl,
        public readonly array $characters,
    ) {}

    public static function fromSwapi(array $data): self
    {
        return new self(
            title: $data['title'],
            openingCrawl: $data['opening_crawl'],
            characters: $data['characters'] ?? [],
        );
    }
}
