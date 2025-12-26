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
        return config('services.swapi.base_url', 'https://swapi.dev/api');
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
        $url = $this->getBaseUrl().'/people';
        $response = Http::get($url, ['search' => $query]);

        if (! $response->successful()) {
            \Log::warning('SWAPI search failed', [
                'url' => $url,
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $data = $response->json();

        // Handle both response formats:
        // swapi.dev returns: {"results": [...]}
        // swapi.info returns: [...]
        $results = is_array($data) && isset($data[0]) && ! isset($data['results'])
            ? $data  // Direct array format (swapi.info)
            : ($data['results'] ?? []);  // Wrapped format (swapi.dev)

        if (! is_array($results) || empty($results)) {
            return [];
        }

        return array_map(
            fn ($item) => CharacterDto::fromSwapi($item),
            $results
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
        $url = $this->getBaseUrl().'/films';
        $response = Http::get($url, ['search' => $query]);

        if (! $response->successful()) {
            \Log::warning('SWAPI search failed', [
                'url' => $url,
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $data = $response->json();

        // Handle both response formats:
        // swapi.dev returns: {"results": [...]}
        // swapi.info returns: [...]
        $results = is_array($data) && isset($data[0]) && ! isset($data['results'])
            ? $data  // Direct array format (swapi.info)
            : ($data['results'] ?? []);  // Wrapped format (swapi.dev)

        if (! is_array($results) || empty($results)) {
            return [];
        }

        return array_map(
            fn ($item) => MovieDto::fromSwapi($item),
            $results
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
     * Get a single movie by ID.
     */
    public function getMovieById(int $id): MovieDto
    {
        $baseUrl = $this->getBaseUrl();
        $response = Cache::remember("swapi_films_{$id}", 3600, function () use ($id, $baseUrl) {
            $response = Http::get("{$baseUrl}/films/{$id}");

            if ($response->failed()) {
                throw new \Exception("Failed to fetch movie: {$response->status()}");
            }

            return $response->json();
        });

        return MovieDto::fromSwapi($response);
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
