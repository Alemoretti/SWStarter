<?php

namespace Tests\Unit;

use App\DTOs\MovieDto;
use App\Http\Resources\MovieResource;
use Tests\TestCase;

class MovieResourceTest extends TestCase
{
    public function test_movie_resource_transforms_dto_correctly(): void
    {
        $dto = new MovieDto(
            title: 'A New Hope',
            openingCrawl: 'It is a period of civil war...',
            characters: ['https://swapi.dev/api/people/1/']
        );

        $resource = new MovieResource($dto);
        $array = $resource->toArray(request());

        $this->assertEquals('A New Hope', $array['title']);
        $this->assertStringContainsString('civil war', $array['opening_crawl']);
    }

    public function test_movie_resource_includes_all_fields(): void
    {
        $dto = new MovieDto(
            title: 'Return of the Jedi',
            openingCrawl: 'Luke Skywalker has returned...',
            characters: []
        );

        $resource = new MovieResource($dto);
        $array = $resource->toArray(request());

        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('opening_crawl', $array);
    }
}