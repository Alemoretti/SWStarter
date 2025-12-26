<?php

namespace App\DTOs;

class MovieDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $openingCrawl,
        public readonly array $characters,
    ) {}

    public static function fromSwapi(array $data): self
    {
        // Extract ID from URL like "https://swapi.dev/api/films/1/"
        $id = self::extractIdFromUrl($data['url'] ?? '');

        return new self(
            id: $id,
            title: $data['title'],
            openingCrawl: $data['opening_crawl'],
            characters: $data['characters'] ?? [],
        );
    }

    private static function extractIdFromUrl(string $url): int
    {
        if (preg_match('/\/(\d+)\/?$/', $url, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
