<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\UserResetPassRequest;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;

class AccountController extends Controller
{
    
   public function __construct(
        private readonly AccountService $accountService
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

    public function resendCode(VerifyUserRequest $request): JsonResponse
    {
        return $this->accountService->resendCode($request);
    }
}
