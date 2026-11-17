<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    /**
     * Return a success JSON response.
     *
     * @param  string  $message
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(mixed $data = null, ?string $message = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
        ], $statusCode);
    }

    /**
     * Return a custom paginated response.
     *
     * @param  LengthAwarePaginator<int, mixed>  $resource
     */
    protected function paginatedResponse(LengthAwarePaginator $resource, string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource->items(),
            'meta' => [
                'current_page' => $resource->currentPage(),
                'total' => $resource->total(),
                'per_page' => $resource->perPage(),
                'last_page' => $resource->lastPage(),
            ],
        ], $statusCode);
    }
}
