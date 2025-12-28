<?php

namespace App\Services;

use App\DTOs\CharacterDto;
use App\DTOs\MovieDto;
use Illuminate\Support\Facades\Cache;

class SwapiService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly SwapiClient $client
    ) {}

    /**
     * Create a new instance with default client.
     *
     * @return self
     */
    public static function make(): self
    {
        $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');
        $client = new SwapiClient($baseUrl);

        return new self($client);
    }

    /**
     * Search for people in SWAPI.
     *
     * @param  string  $query
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
     * @param  string  $query
     * @return array<int, CharacterDto>
     */
    private function fetchPeople(string $query): array
    {
        $data = $this->client->get('people', ['search' => $query]);

        if ($data === null) {
            return [];
        }

        $results = $this->client->extractResults($data);
        $filtered = $this->client->filterResults($results, $query, 'name');

        return array_map(
            fn ($item) => CharacterDto::fromSwapi($item),
            $filtered
        );
    }

    /**
     * Search for films in SWAPI.
     *
     * @param  string  $query
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
     * @param  string  $query
     * @return array<int, MovieDto>
     */
    private function fetchFilms(string $query): array
    {
        $data = $this->client->get('films', ['search' => $query]);

        if ($data === null) {
            return [];
        }

        $results = $this->client->extractResults($data);
        $filtered = $this->client->filterResults($results, $query, 'title');

        return array_map(
            fn ($item) => MovieDto::fromSwapi($item),
            array_values($filtered)
        );
    }

    /**
     * Get a single character by ID.
     *
     * @param  int  $id
     * @return CharacterDto
     * @throws \Exception
     */
    public function getCharacter(int $id): CharacterDto
    {
        $data = Cache::remember("swapi_people_{$id}", 3600, function () use ($id) {
            $response = $this->client->get("people/{$id}");

            if ($response === null) {
                throw new \Exception("Failed to fetch character: {$id}");
            }

            return $response;
        });

        return CharacterDto::fromSwapi($data);
    }

    /**
     * Get a single movie by ID.
     *
     * @param  int  $id
     * @return MovieDto
     * @throws \Exception
     */
    public function getMovieById(int $id): MovieDto
    {
        $data = Cache::remember("swapi_films_{$id}", 3600, function () use ($id) {
            $response = $this->client->get("films/{$id}");

            if ($response === null) {
                throw new \Exception("Failed to fetch movie: {$id}");
            }

            return $response;
        });

        return MovieDto::fromSwapi($data);
    }

    /**
     * Get a movie by URL from SWAPI.
     *
     * @param  string  $url
     * @return MovieDto|null
     */
    public function getMovie(string $url): ?MovieDto
    {
        // Extract endpoint from full URL
        $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');
        $endpoint = str_replace($baseUrl.'/', '', $url);

        $data = $this->client->get($endpoint);

        if ($data === null) {
            return null;
        }

        return MovieDto::fromSwapi($data);
    }
}
