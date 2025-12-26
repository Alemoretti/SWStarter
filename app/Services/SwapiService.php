<?php

namespace App\Services;

use App\DTOs\CharacterDto;
use App\DTOs\MovieDto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SwapiService
{
    private const BASE_URL = 'https://swapi.dev/api';

    private const CACHE_TTL = 3600; // 1 hour

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
        $response = Http::get(self::BASE_URL.'/people', ['search' => $query]);

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
     * Search for movies in SWAPI.
     *
     * @return array<int, MovieDto>
     */
    public function searchMovies(string $query): array
    {
        return Cache::remember(
            "swapi.movies.search.{$query}",
            self::CACHE_TTL,
            fn () => $this->fetchMovies($query)
        );
    }

    /**
     * Fetch movies from SWAPI.
     *
     * @return array<int, MovieDto>
     */
    private function fetchMovies(string $query): array
    {
        $response = Http::get(self::BASE_URL.'/films', ['search' => $query]);

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
     * Get a character by URL from SWAPI.
     */
    public function getCharacter(string $url): ?CharacterDto
    {
        $response = Http::get($url);

        if (! $response->successful()) {
            return null;
        }

        return CharacterDto::fromSwapi($response->json());
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
