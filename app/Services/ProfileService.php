<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class ProfileService
{
    use HttpResponses;

    public function profile(#[CurrentUser] User $user): JsonResponse
    {
        return $this->successResponse(new UserResource($user), 'Profile retrieved successfully.');
    }

    public function logout(#[CurrentUser] User $user): JsonResponse
    {
        $user->currentAccessToken()->delete();

        return $this->successResponse(
            null,
            'Logged out successfully.'
        );
    }
}
