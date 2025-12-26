<?php

namespace App\Http\Resources;

use App\DTOs\CharacterDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CharacterDto $dto */
        $dto = $this->resource;

        return [
            'name' => $dto->name,
            'birth_year' => $dto->birthYear,
            'gender' => $dto->gender,
            'eye_color' => $dto->eyeColor,
            'hair_color' => $dto->hairColor,
            'height' => $dto->height,
            'mass' => $dto->mass,
        ];
    }
}
