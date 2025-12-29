<?php

namespace App\Services;

use App\DTOs\CharacterDto;
use App\DTOs\MovieDto;
use App\Exceptions\SwapiException;
use Illuminate\Support\Facades\Cache;

class SwapiService
{
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly SwapiClient $client
    ) {}

    /**
     * Create a new instance with default client.
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
        try {
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
        } catch (\Exception $e) {
            return [];
        }
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
        try {
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
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get a single character by ID.
     *
     * @throws SwapiException
     */
    public function getCharacter(int $id): CharacterDto
    {
        try {
            $data = Cache::remember("swapi_people_{$id}", 3600, function () use ($id) {
                return $this->client->get("people/{$id}");
            });

            return CharacterDto::fromSwapi($data);
        } catch (SwapiException $e) {
            throw $e;
        }
    }

    /**
     * Get a single movie by ID.
     *
     * @throws SwapiException
     */
    public function getMovieById(int $id): MovieDto
    {
        try {
            $data = Cache::remember("swapi_films_{$id}", 3600, function () use ($id) {
                return $this->client->get("films/{$id}");
            });

            return MovieDto::fromSwapi($data);
        } catch (SwapiException $e) {
            throw $e;
        }
    }

    /**
     * Get a movie by URL from SWAPI.
     */
    public function getMovie(string $url): ?MovieDto
    {
        try {
            // Extract endpoint from full URL
            $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');
            $endpoint = str_replace($baseUrl.'/', '', $url);

            $data = $this->client->get($endpoint);

            return MovieDto::fromSwapi($data);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get multiple movies by URLs concurrently.
     *
     * @param  array<string>  $urls
     * @return array<int, MovieDto>
     */
    public function getMovies(array $urls): array
    {
        if (empty($urls)) {
            return [];
        }

        $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');

        $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($urls, $baseUrl) {
            return collect($urls)->map(function ($url) use ($pool, $baseUrl) {
                $endpoint = str_replace($baseUrl.'/', '', $url);

                return $pool->get($baseUrl.'/'.$endpoint);
            });
        });

        $movies = [];
        foreach ($responses as $response) {
            if ($response->successful()) {
                try {
                    $movie = MovieDto::fromSwapi($response->json());
                    if ($movie) {
                        $movies[] = $movie;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $movies;
    }

    /**
     * Get multiple characters by IDs concurrently.
     *
     * @param  array<int>  $ids
     * @return array<int, CharacterDto>
     */
    public function getCharacters(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');

        $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($ids, $baseUrl) {
            return collect($ids)->map(function ($id) use ($pool, $baseUrl) {
                return $pool->get("{$baseUrl}/people/{$id}");
            });
        });

        $characters = [];
        foreach ($responses as $response) {
            if ($response->successful()) {
                try {
                    $character = CharacterDto::fromSwapi($response->json());
                    if ($character) {
                        $characters[] = $character;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $characters;
    }
}
