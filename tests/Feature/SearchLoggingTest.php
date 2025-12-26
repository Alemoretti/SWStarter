<?php

namespace Tests\Feature;

use App\Models\SearchQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_logs_query_to_database(): void
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

        $this->postJson('/api/v1/search', [
            'query' => 'luke',
            'type' => 'people',
        ]);

        $this->assertDatabaseHas('search_queries', [
            'query' => 'luke',
            'type' => 'people',
            'results_count' => 1,
        ]);
    }

    public function test_search_logs_response_time(): void
    {
        Http::fake([
            '*/api/people*' => Http::response([
                'results' => [],
            ], 200),
        ]);

        $this->postJson('/api/v1/search', [
            'query' => 'test',
            'type' => 'people',
        ]);

        $query = SearchQuery::where('query', 'test')->first();
        $this->assertNotNull($query->response_time_ms);
        $this->assertIsInt($query->response_time_ms);
    }

    public function test_search_logs_results_count(): void
    {
        Http::fake([
            '*/api/people*' => Http::response([
                'results' => [
                    ['name' => 'Luke', 'birth_year' => '19BBY', 'gender' => 'male', 'eye_color' => 'blue', 'hair_color' => 'blond', 'height' => '172', 'mass' => '77', 'films' => []],
                    ['name' => 'Yoda', 'birth_year' => '896BBY', 'gender' => 'male', 'eye_color' => 'brown', 'hair_color' => 'white', 'height' => '66', 'mass' => '17', 'films' => []],
                ],
            ], 200),
        ]);

        $this->postJson('/api/v1/search', [
            'query' => 'test',
            'type' => 'people',
        ]);

        $this->assertDatabaseHas('search_queries', [
            'query' => 'test',
            'results_count' => 2,
        ]);
    }

    public function test_search_logs_movies_type(): void
    {
        Http::fake([
            '*/api/films*' => Http::response([
                'results' => [],
            ], 200),
        ]);

        $this->postJson('/api/v1/search', [
            'query' => 'hope',
            'type' => 'movies',
        ]);

        $this->assertDatabaseHas('search_queries', [
            'query' => 'hope',
            'type' => 'movies',
        ]);
    }
}
