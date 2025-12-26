<?php

namespace Tests\Unit;

use App\DTOs\CharacterDto;
use App\DTOs\MovieDto;
use App\Services\SwapiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SwapiServiceTest extends TestCase
{
    public function test_search_people_returns_character_dtos(): void
    {
        Http::fake([
            '*/api/people*' => Http::response([
                'results' => [[
                    'name' => 'Luke Skywalker',
                    'birth_year' => '19BBY',
                    'gender' => 'male',
                    'eye_color' => 'blue',
                    'hair_color' => 'blond',
                    'height' => '172',
                    'mass' => '77',
                    'films' => ['https://swapi.dev/api/films/1/'],
                ]],
            ], 200),
        ]);

        $service = new SwapiService;
        $results = $service->searchPeople('luke');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(CharacterDto::class, $results[0]);
        $this->assertEquals('Luke Skywalker', $results[0]->name);
    }

    public function test_search_people_returns_empty_array_on_api_error(): void
    {
        Http::fake([
            '*/api/people*' => Http::response([], 500),
        ]);

        $service = new SwapiService;
        $results = $service->searchPeople('luke');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_search_people_caches_results(): void
    {
        Http::fake([
            '*/api/people*' => Http::response([
                'results' => [[
                    'name' => 'Luke Skywalker',
                    'birth_year' => '19BBY',
                    'gender' => 'male',
                    'eye_color' => 'blue',
                    'hair_color' => 'blond',
                    'height' => '172',
                    'mass' => '77',
                    'films' => [],
                ]],
            ], 200),
        ]);

        $service = new SwapiService;

        // First call - should hit API
        $service->searchPeople('luke');

        // Second call - should use cache
        $service->searchPeople('luke');

        // Should only make one HTTP request
        Http::assertSentCount(1);
    }

    public function test_search_films_returns_movie_dtos(): void
    {
        Http::fake([
            '*/api/films*' => Http::response([
                'results' => [[
                    'title' => 'A New Hope',
                    'opening_crawl' => 'It is a period of civil war...',
                    'characters' => ['https://swapi.dev/api/people/1/'],
                ]],
            ], 200),
        ]);

        $service = new SwapiService;
        $results = $service->searchFilms('hope');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(MovieDto::class, $results[0]);
        $this->assertEquals('A New Hope', $results[0]->title);
    }

    public function test_get_character_returns_character_dto(): void
    {
        Http::fake([
            '*/api/people/1' => Http::response([
                'name' => 'Luke Skywalker',
                'birth_year' => '19BBY',
                'gender' => 'male',
                'eye_color' => 'blue',
                'hair_color' => 'blond',
                'height' => '172',
                'mass' => '77',
                'films' => [],
            ], 200),
        ]);

        $service = new SwapiService;
        $character = $service->getCharacter(1);

        $this->assertInstanceOf(CharacterDto::class, $character);
        $this->assertEquals('Luke Skywalker', $character->name);
    }

    public function test_get_character_throws_exception_on_error(): void
    {
        Http::fake([
            '*/api/people/1' => Http::response([], 404),
        ]);

        $service = new SwapiService;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('404');

        $service->getCharacter(1);
    }

    public function test_get_movie_returns_movie_dto(): void
    {
        Http::fake([
            'https://swapi.dev/api/films/1/' => Http::response([
                'title' => 'A New Hope',
                'opening_crawl' => 'It is a period of civil war...',
                'characters' => [],
            ], 200),
        ]);

        $service = new SwapiService;
        $movie = $service->getMovie('https://swapi.dev/api/films/1/');

        $this->assertInstanceOf(MovieDto::class, $movie);
        $this->assertEquals('A New Hope', $movie->title);
    }
}
