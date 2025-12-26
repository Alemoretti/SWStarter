<?php

namespace App\DTOs;

class CharacterDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $birthYear,
        public readonly string $gender,
        public readonly string $eyeColor,
        public readonly string $hairColor,
        public readonly ?int $height,
        public readonly ?int $mass,
        public readonly array $films,
    ) {}

    public static function fromSwapi(array $data): self
    {
        // Extract ID from URL like "https://swapi.dev/api/people/1/"
        $id = self::extractIdFromUrl($data['url'] ?? '');

        return new self(
            id: $id,
            name: $data['name'],
            birthYear: $data['birth_year'] === 'unknown' ? null : $data['birth_year'],
            gender: $data['gender'],
            eyeColor: $data['eye_color'],
            hairColor: $data['hair_color'],
            height: $data['height'] === 'unknown' ? null : (int) $data['height'],
            mass: $data['mass'] === 'unknown' ? null : (int) str_replace(',', '', $data['mass']),
            films: $data['films'] ?? [],
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
