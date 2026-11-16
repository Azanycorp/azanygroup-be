<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\UserResetPassRequest;
use App\Services\AccountService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use HttpResponses;

   public function __construct(
        private AccountService $accountService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->accountService->login($request);
    }
    
    public function verifyUser(VerifyUserRequest $request): JsonResponse
    {
        return $this->accountService->verifyUserIdentity($request);
    }

    public function verifyCode(CodeRequest $request): JsonResponse
    {
        return $this->accountService->verifyCode($request);
    }

    public function changePassword(UserResetPassRequest $request): JsonResponse
    {
        return $this->accountService->changePassword($request);
    }

    public function resendCode(Request $request): JsonResponse
    {
        return $this->accountService->resendCode($request);
    }
}
