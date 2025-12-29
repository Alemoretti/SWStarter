<?php

namespace App\Http\Controllers\Concerns;

use App\Exceptions\SwapiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

trait HandlesSwapiErrors
{
    protected function handleSwapiError(
        \Throwable $e,
        string $resourceName,
        string $pageName,
        Request $request
    ): JsonResponse|InertiaResponse {
        $statusCode = 500;
        $message = 'External API error';

        if ($e instanceof SwapiException) {
            $statusCode = $e->statusCode;
            $message = $statusCode === 404
                ? "{$resourceName} not found"
                : 'External API error';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'error' => $message,
            ], $statusCode);
        }

        return Inertia::render($pageName, [
            'error' => $message,
        ])->toResponse($request)->setStatusCode($statusCode);
    }
}

