<?php

namespace App\Http\Resources;

use App\DTOs\MovieDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MovieDto $dto */
        $dto = $this->resource;

        return [
            'id' => $dto->id,
            'title' => $dto->title,
            'opening_crawl' => $dto->openingCrawl,
            'characters' => $dto->characters,
        ];
    }
}
