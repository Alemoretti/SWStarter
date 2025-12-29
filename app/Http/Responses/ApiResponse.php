<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data, array $meta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], $status);
    }
}
