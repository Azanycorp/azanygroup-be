<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendEmailVerificationOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use App\Http\Requests\Auth\VerifyPasswordResetOtpRequest;
use App\Services\AccountService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    use HttpResponses;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        public AccountService $accountService,
    ) {}

    /**
     * Send or resend an email verification OTP code.
     */
    public function sendVerificationCode(SendEmailVerificationOtpRequest $request): JsonResponse
    {
        $result = $this->accountService->sendVerificationCode($request->targetEmail());

        if (! $result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? null,
                $result['message'],
                $result['status'],
            );
        }

        return $this->successResponse(
            $result['data'] ?? null,
            $result['message'],
            $result['status'],
        );
    }

    /**
     * Verify the user's email address using the provided OTP code.
     */
    public function verifyEmail(VerifyEmailOtpRequest $request): JsonResponse
    {
        $result = $this->accountService->verifyEmail(
            $request->targetEmail(),
            (string) $request->string('code'),
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? null,
                $result['message'],
                $result['status'],
            );
        }

        return $this->successResponse(
            $result['data'] ?? null,
            $result['message'],
            $result['status'],
        );
    }

    /**
     * Send a 6-digit password reset OTP to the user's email.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->accountService->sendPasswordResetCode(
            strtolower(trim((string) $request->string('email'))),
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? null,
                $result['message'],
                $result['status'],
            );
        }

        return $this->successResponse(
            $result['data'] ?? null,
            $result['message'],
            $result['status'],
        );
    }

    /**
     * Verify whether a password reset OTP code is valid before prompting the new password.
     */
    public function verifyPasswordResetCode(VerifyPasswordResetOtpRequest $request): JsonResponse
    {
        $result = $this->accountService->verifyPasswordResetCode(
            strtolower(trim((string) $request->string('email'))),
            (string) $request->string('code'),
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? null,
                $result['message'],
                $result['status'],
            );
        }

        return $this->successResponse(
            $result['data'] ?? null,
            $result['message'],
            $result['status'],
        );
    }

    /**
     * Reset the user's password using the verified OTP code.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->accountService->resetPassword(
            strtolower(trim((string) $request->string('email'))),
            (string) $request->string('code'),
            (string) $request->string('password'),
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? null,
                $result['message'],
                $result['status'],
            );
        }

        return $this->successResponse(
            $result['data'] ?? null,
            $result['message'],
            $result['status'],
        );
    }
}
