<?php
namespace App\Services;
use App\Enum\MailingEnum;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UserResetPassRequest;
use App\Http\Requests\Auth\CodeRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Container\Attributes\CurrentUser;

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
