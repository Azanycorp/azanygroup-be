<?php
namespace App\Services;
use App\Enum\MailingEnum;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Container\Attributes\CurrentUser;

class AdminService
{
    use HttpResponses;
    public function dashboard(): JsonResponse
    {
        return $this->successResponse(new UserResource($user), 'Dashboard data retrieved successfully.');
    }

}
