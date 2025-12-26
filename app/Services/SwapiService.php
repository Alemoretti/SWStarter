<?php

namespace App\Services;

use App\DTOs\CharacterDto;
use App\DTOs\MovieDto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SwapiService
{
    private const CACHE_TTL = 3600; // 1 hour

    private function getBaseUrl(): string
    {
        return env('SWAPI_BASE_URL', 'https://swapi.dev/api');
    }

    /**
     * Search for people in SWAPI.
     *
     * @return array<int, CharacterDto>
     */
    public function searchPeople(string $query): array
    {
        return Cache::remember(
            "swapi.people.search.{$query}",
            self::CACHE_TTL,
            fn () => $this->fetchPeople($query)
        );
    }

    /**
     * Fetch people from SWAPI.
     *
     * @return array<int, CharacterDto>
     */
    private function fetchPeople(string $query): array
    {
        $response = Http::get($this->getBaseUrl().'/people', ['search' => $query]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return array_map(
            fn ($item) => CharacterDto::fromSwapi($item),
            $data['results'] ?? []
        );
    }

    /**
     * Search for films in SWAPI.
     *
     * @return array<int, MovieDto>
     */
    public function searchFilms(string $query): array
    {
        return Cache::remember(
            "swapi.films.search.{$query}",
            self::CACHE_TTL,
            fn () => $this->fetchFilms($query)
        );
    }

    /**
     * Fetch films from SWAPI.
     *
     * @return array<int, MovieDto>
     */
    private function fetchFilms(string $query): array
    {
        $response = Http::get($this->getBaseUrl().'/films', ['search' => $query]);

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return array_map(
            fn ($item) => MovieDto::fromSwapi($item),
            $data['results'] ?? []
        );
    }

    /**
     * Get a single character by ID.
     */
    public function getCharacter(int $id): CharacterDto
    {
        $baseUrl = $this->getBaseUrl();
        $response = Cache::remember("swapi_people_{$id}", 3600, function () use ($id, $baseUrl) {
            $response = Http::get("{$baseUrl}/people/{$id}");
            
            if ($response->failed()) {
                throw new \Exception("Failed to fetch character: {$response->status()}");
            }
            
            return $response->json();
        });

        return CharacterDto::fromSwapi($response);
    }

    /**
     * Get a movie by URL from SWAPI.
     */
    public function getMovie(string $url): ?MovieDto
    {
        $response = Http::get($url);

        if (! $response->successful()) {
            return null;
        }

        return MovieDto::fromSwapi($response->json());
    }
}
