<?php

namespace Tests\Unit;

use App\DTOs\FilmDto;
use Tests\TestCase;

class FilmDtoTest extends TestCase
{
    public function test_film_dto_can_be_created(): void
    {
        $dto = new FilmDto(
            title: 'A New Hope',
            openingCrawl: 'It is a period of civil war...',
            characters: ['https://swapi.dev/api/people/1/']
        );

        $this->assertEquals('A New Hope', $dto->title);
        $this->assertStringContainsString('civil war', $dto->openingCrawl);
        $this->assertIsArray($dto->characters);
    }

    public function test_film_dto_can_be_created_from_swapi_data(): void
    {
        $swapiData = [
            'title' => 'A New Hope',
            'opening_crawl' => 'It is a period of civil war...',
            'characters' => ['https://swapi.dev/api/people/1/'],
        ];

        $dto = FilmDto::fromSwapi($swapiData);

        $this->assertEquals('A New Hope', $dto->title);
        $this->assertStringContainsString('civil war', $dto->openingCrawl);
    }

    public function test_film_dto_handles_missing_characters(): void
    {
        $swapiData = [
            'title' => 'A New Hope',
            'opening_crawl' => 'It is a period of civil war...',
        ];

        $dto = FilmDto::fromSwapi($swapiData);

        $this->assertIsArray($dto->characters);
        $this->assertEmpty($dto->characters);
    }
}
