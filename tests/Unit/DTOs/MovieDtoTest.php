<?php

namespace Tests\Unit\DTOs;

use App\DTOs\MovieDto;
use Tests\TestCase;

class MovieDtoTest extends TestCase
{
    public function test_movie_dto_can_be_created(): void
    {
        $dto = new MovieDto(
            id: 1,
            title: 'A New Hope',
            openingCrawl: 'It is a period of civil war...',
            characters: ['https://swapi.dev/api/people/1/']
        );

        $this->assertEquals('A New Hope', $dto->title);
        $this->assertStringContainsString('civil war', $dto->openingCrawl);
        $this->assertIsArray($dto->characters);
    }

    public function test_movie_dto_can_be_created_from_swapi_data(): void
    {
        $swapiData = [
            'url' => 'https://swapi.dev/api/films/1/',
            'title' => 'A New Hope',
            'opening_crawl' => 'It is a period of civil war...',
            'characters' => ['https://swapi.dev/api/people/1/'],
        ];

        $dto = MovieDto::fromSwapi($swapiData);

        $this->assertEquals('A New Hope', $dto->title);
        $this->assertStringContainsString('civil war', $dto->openingCrawl);
    }

    public function test_movie_dto_handles_missing_characters(): void
    {
        $swapiData = [
            'url' => 'https://swapi.dev/api/films/1/',
            'title' => 'A New Hope',
            'opening_crawl' => 'It is a period of civil war...',
        ];

        $dto = MovieDto::fromSwapi($swapiData);

        $this->assertIsArray($dto->characters);
        $this->assertEmpty($dto->characters);
    }
}
