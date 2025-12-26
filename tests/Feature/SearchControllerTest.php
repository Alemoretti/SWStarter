<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_people_returns_character_resources(): void
    {
        Http::fake([
            'https://swapi.dev/api/people*' => Http::response([
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

        $response = $this->postJson('/api/v1/search', [
            'query' => 'luke',
            'type' => 'people',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'birth_year', 'gender', 'eye_color', 'hair_color', 'height', 'mass'],
                ],
            ]);
    }

    public function test_search_movies_returns_movie_resources(): void
    {
        Http::fake([
            'https://swapi.dev/api/films*' => Http::response([
                'results' => [[
                    'title' => 'A New Hope',
                    'opening_crawl' => 'It is a period of civil war...',
                    'characters' => [],
                ]],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/search', [
            'query' => 'hope',
            'type' => 'movies',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['title', 'opening_crawl'],
                ],
            ]);
    }

    public function test_search_validates_query_is_required(): void
    {
        $response = $this->postJson('/api/v1/search', [
            'type' => 'people',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_search_validates_type_is_required(): void
    {
        $response = $this->postJson('/api/v1/search', [
            'query' => 'luke',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_search_validates_type_must_be_people_or_movies(): void
    {
        $response = $this->postJson('/api/v1/search', [
            'query' => 'luke',
            'type' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_search_returns_empty_array_when_no_results(): void
    {
        Http::fake([
            'https://swapi.dev/api/people*' => Http::response([
                'results' => [],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/search', [
            'query' => 'nonexistent',
            'type' => 'people',
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }
}
