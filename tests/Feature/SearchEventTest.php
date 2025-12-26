<?php

namespace Tests\Feature;

use App\Events\SearchPerformed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_performed_event_is_fired(): void
    {
        Event::fake();

        Http::fake([
            'https://swapi.dev/api/people*' => Http::response([
                'results' => []
            ], 200)
        ]);

        $this->postJson('/api/v1/search', [
            'query' => 'luke',
            'type' => 'people',
        ]);

        Event::assertDispatched(SearchPerformed::class, function ($event) {
            return $event->query === 'luke' 
                && $event->type === 'people'
                && $event->resultsCount === 0;
        });
    }

    public function test_search_performed_event_contains_correct_data(): void
    {
        Event::fake();

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
                ]]
            ], 200)
        ]);

        $this->postJson('/api/v1/search', [
            'query' => 'test',
            'type' => 'people',
        ]);

        Event::assertDispatched(SearchPerformed::class, function ($event) {
            return $event->query === 'test' 
                && $event->type === 'people'
                && $event->resultsCount === 1;
        });
    }
}