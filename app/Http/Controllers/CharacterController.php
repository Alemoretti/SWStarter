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
            $statusCode = 404;
            $message = 'Character not found';
            
            // Extract status code from exception message (format: "Failed to fetch character: 500")
            if (preg_match('/: (\d+)$/', $e->getMessage(), $matches)) {
                $statusCode = (int) $matches[1];
                if ($statusCode >= 500) {
                    $message = 'External API error';
                } elseif ($statusCode === 404) {
                    $message = 'Character not found';
                }
            }
            
            return response()->json([
                'error' => $message
            ], $statusCode);
        }
    }
}