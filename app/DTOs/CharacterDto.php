<?php

namespace App\DTOs;

class CharacterDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $birthYear,
        public readonly string $gender,
        public readonly string $eyeColor,
        public readonly string $hairColor,
        public readonly ?int $height,
        public readonly ?int $mass,
        public readonly array $movies,
    ) {}

    public static function fromSwapi(array $data): self
    {
        return new self(
            name: $data['name'],
            birthYear: $data['birth_year'] === 'unknown' ? null : $data['birth_year'],
            gender: $data['gender'],
            eyeColor: $data['eye_color'],
            hairColor: $data['hair_color'],
            height: $data['height'] === 'unknown' ? null : (int) $data['height'],
            mass: $data['mass'] === 'unknown' ? null : (int) str_replace(',', '', $data['mass']),
            movies: $data['films'] ?? [],
        );
    }
}
