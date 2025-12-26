<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovieDetailTest extends TestCase
{
    public function test_movie_detail_endpoint_returns_movie_data(): void
    {
        Http::fake([
            '*/api/films/1' => Http::response([
                'title' => 'A New Hope',
                'opening_crawl' => 'It is a period of civil war...',
                'characters' => [
                    'https://swapi.dev/api/people/1/',
                    'https://swapi.dev/api/people/2/',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/movies/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'opening_crawl',
                    'characters',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'A New Hope',
                    'opening_crawl' => 'It is a period of civil war...',
                ],
            ]);
    }

    public function test_movie_detail_returns_404_for_invalid_id(): void
    {
        Http::fake([
            '*/api/films/999' => Http::response([
                'detail' => 'Not found',
            ], 404),
        ]);

        $response = $this->getJson('/api/v1/movies/999');

        $response->assertStatus(404);
    }

    public function test_movie_detail_handles_api_errors(): void
    {
        Http::fake([
            '*/api/films/1' => Http::response([
                'error' => 'Internal Server Error',
            ], 500),
        ]);

        $response = $this->getJson('/api/v1/movies/1');

        $response->assertStatus(500);
    }
}
