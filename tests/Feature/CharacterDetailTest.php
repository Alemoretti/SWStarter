<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CharacterDetailTest extends TestCase
{
    public function test_character_detail_endpoint_returns_character_data(): void
    {
        Http::fake([
            'https://swapi.dev/api/people/1' => Http::response([
                'name' => 'Luke Skywalker',
                'birth_year' => '19BBY',
                'gender' => 'male',
                'eye_color' => 'blue',
                'hair_color' => 'blond',
                'height' => '172',
                'mass' => '77',
                'films' => [
                    'https://swapi.dev/api/films/1/',
                    'https://swapi.dev/api/films/2/',
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/characters/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'birth_year',
                    'gender',
                    'eye_color',
                    'hair_color',
                    'height',
                    'mass',
                    'films',
                ],
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Luke Skywalker',
                    'birth_year' => '19BBY',
                    'gender' => 'male',
                    'eye_color' => 'blue',
                    'hair_color' => 'blond',
                    'height' => '172',
                    'mass' => '77',
                ],
            ]);
    }

    public function test_character_detail_returns_404_for_invalid_id(): void
    {
        Http::fake([
            'https://swapi.dev/api/people/999' => Http::response([
                'detail' => 'Not found',
            ], 404),
        ]);

        $response = $this->getJson('/api/v1/characters/999');

        $response->assertStatus(404);
    }

    public function test_character_detail_handles_api_errors(): void
    {
        Http::fake([
            '*/api/people/2' => Http::response([
                'error' => 'Internal Server Error'
            ], 500),
        ]);

        $response = $this->getJson('/api/v1/characters/2');

        $response->assertStatus(500);
    }
}
