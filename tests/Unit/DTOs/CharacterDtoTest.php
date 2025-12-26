<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CharacterDto;
use Tests\TestCase;

class CharacterDtoTest extends TestCase
{
    public function test_character_dto_can_be_created(): void
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

        $this->assertEquals('Luke Skywalker', $dto->name);
        $this->assertEquals('19BBY', $dto->birthYear);
        $this->assertEquals('male', $dto->gender);
        $this->assertEquals('blue', $dto->eyeColor);
        $this->assertEquals('blond', $dto->hairColor);
        $this->assertEquals(172, $dto->height);
        $this->assertEquals(77, $dto->mass);
        $this->assertIsArray($dto->films);
    }

    public function test_character_dto_can_be_created_from_swapi_data(): void
    {
        $swapiData = [
            'name' => 'Luke Skywalker',
            'birth_year' => '19BBY',
            'gender' => 'male',
            'eye_color' => 'blue',
            'hair_color' => 'blond',
            'height' => '172',
            'mass' => '77',
            'films' => ['https://swapi.dev/api/films/1/'],
        ];

        $dto = CharacterDto::fromSwapi($swapiData);

        $this->assertEquals('Luke Skywalker', $dto->name);
        $this->assertEquals('19BBY', $dto->birthYear);
        $this->assertEquals(172, $dto->height);
        $this->assertEquals(77, $dto->mass);
    }

    public function test_character_dto_handles_unknown_values(): void
    {
        $swapiData = [
            'name' => 'Unknown Character',
            'birth_year' => 'unknown',
            'gender' => 'n/a',
            'eye_color' => 'unknown',
            'hair_color' => 'none',
            'height' => 'unknown',
            'mass' => 'unknown',
            'films' => [],
        ];

        $dto = CharacterDto::fromSwapi($swapiData);

        $this->assertNull($dto->birthYear);
        $this->assertNull($dto->height);
        $this->assertNull($dto->mass);
    }

    public function test_character_dto_handles_mass_with_commas(): void
    {
        $swapiData = [
            'name' => 'Test Character',
            'birth_year' => '19BBY',
            'gender' => 'male',
            'eye_color' => 'blue',
            'hair_color' => 'blond',
            'height' => '172',
            'mass' => '1,234', // Mass with comma
            'films' => [],
        ];

        $dto = CharacterDto::fromSwapi($swapiData);

        $this->assertEquals(1234, $dto->mass);
    }
}
