<?php

namespace App\Http\Resources;

use App\DTOs\FilmDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FilmDto $dto */
        $dto = $this->resource;

        return [
            'title' => $dto->title,
            'opening_crawl' => $dto->openingCrawl,
            'characters' => $dto->characters,
        ];
    }
}
