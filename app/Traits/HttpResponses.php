<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;

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
     * @param  LengthAwarePaginator<int, mixed>|AnonymousResourceCollection  $resource
     */
    protected function paginatedResponse(
        LengthAwarePaginator|AnonymousResourceCollection $resource, // <-- MUST include both type hints
        string $message,
        int $statusCode = 200
    ): JsonResponse {
        // Determine the actual paginator
        $paginator = $resource instanceof AnonymousResourceCollection
            ? $resource->resource
            : $resource;

        if (!$paginator instanceof LengthAwarePaginator) {
           throw new InvalidArgumentException('Expected a LengthAwarePaginator or AnonymousResourceCollection.');
        }

        // Extract raw array items or serialize collection
        $items = $resource instanceof AnonymousResourceCollection
            ? $resource->toArray(request())
            : $paginator->items();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ], $statusCode);
    }
}
