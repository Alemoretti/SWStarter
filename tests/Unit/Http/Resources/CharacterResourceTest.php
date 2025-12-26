<?php

namespace Tests\Unit\Http\Resources;

use App\DTOs\CharacterDto;
use App\Http\Resources\CharacterResource;
use Tests\TestCase;

class CharacterResourceTest extends TestCase
{
    public function test_character_resource_transforms_dto_correctly(): void
    {
        $dto = new CharacterDto(
            name: 'Luke Skywalker',
            birthYear: '19BBY',
            gender: 'male',
            eyeColor: 'blue',
            hairColor: 'blond',
            height: 172,
            mass: 77,
            films: ['https://swapi.dev/api/films/1/']
        );

        $resource = new CharacterResource($dto);
        $array = $resource->toArray(request());

        $this->assertEquals('Luke Skywalker', $array['name']);
        $this->assertEquals('19BBY', $array['birth_year']);
        $this->assertEquals('male', $array['gender']);
        $this->assertEquals('blue', $array['eye_color']);
        $this->assertEquals('blond', $array['hair_color']);
        $this->assertEquals(172, $array['height']);
        $this->assertEquals(77, $array['mass']);
    }

    public function test_character_resource_handles_null_values(): void
    {
        $dto = new CharacterDto(
            name: 'Unknown Character',
            birthYear: null,
            gender: 'n/a',
            eyeColor: 'unknown',
            hairColor: 'none',
            height: null,
            mass: null,
            films: []
        );

        $resource = new CharacterResource($dto);
        $array = $resource->toArray(request());

        $this->assertNull($array['birth_year']);
        $this->assertNull($array['height']);
        $this->assertNull($array['mass']);
    }
}
