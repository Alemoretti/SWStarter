<?php

namespace App\Http\Controllers;

use App\Http\Resources\FilmResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;

class FilmController extends Controller
{
    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Get movie details by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $movie = $this->swapiService->getMovieById($id);

            return response()->json([
                'data' => new FilmResource($movie),
            ]);
        } catch (\Exception $e) {
            $statusCode = 404;
            $message = 'Movie not found';

            // Extract status code from exception message (format: "Failed to fetch movie: 500")
            if (preg_match('/: (\d+)$/', $e->getMessage(), $matches)) {
                $statusCode = (int) $matches[1];
                if ($statusCode >= 500) {
                    $message = 'External API error';
                } elseif ($statusCode === 404) {
                    $message = 'Movie not found';
                }
            }

            return response()->json([
                'error' => $message,
            ], $statusCode);
        }
    }
}
