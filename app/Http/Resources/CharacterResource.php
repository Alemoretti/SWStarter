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
            'name' => $this->name,
            'birth_year' => $this->birthYear,
            'gender' => $this->gender,
            'eye_color' => $this->eyeColor,
            'hair_color' => $this->hairColor,
            'height' => $this->height,
            'mass' => $this->mass,
            'films' => $this->films,
        ];
    }
}
