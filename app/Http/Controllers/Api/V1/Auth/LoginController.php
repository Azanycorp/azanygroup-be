<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $deviceName = (string) ($request->input('device_name') ?: 'api-token');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login successful.', Response::HTTP_OK);
    }

    /**
     * Destroy the authenticated user's current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse(
            null,
            'Logged out successfully.',
            Response::HTTP_OK,
        );
    }

    /**
     * Return the authenticated user's details.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => new UserResource($request->user()),
        ], 'User retrieved successfully.', Response::HTTP_OK);
    }
}
