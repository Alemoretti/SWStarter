<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;

class CharacterController extends Controller
{
    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Get character details by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $character = $this->swapiService->getCharacter($id);
            
            return response()->json([
                'data' => new CharacterResource($character)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Character not found'
            ], 404);
        }
    }
}