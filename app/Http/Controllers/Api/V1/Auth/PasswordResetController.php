<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyPasswordResetOtpRequest;
use App\Models\User;
use App\Notifications\ResetPasswordOtpNotification;
use App\Services\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public OtpService $otpService,
    ) {}

    /**
     * Send a 6-digit password reset OTP to the user's email.
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $email = strtolower(trim((string) $request->string('email')));
        $user = User::where('email', $email)->first();

        if ($user !== null && ! $this->otpService->isThrottled($email, OtpType::PasswordReset)) {
            $code = $this->otpService->generate($email, OtpType::PasswordReset, 15);
            $user->notify(new ResetPasswordOtpNotification($code, 15));
        }

        return response()->json([
            'message' => 'If your email is registered in our system, you will receive a password reset code.',
            'expires_in_minutes' => 15,
        ], Response::HTTP_OK);
    }

    /**
     * Verify whether a password reset OTP code is valid before prompting the new password.
     */
    public function verifyCode(VerifyPasswordResetOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim((string) $request->string('email')));
        $code = (string) $request->string('code');

        $isValid = $this->otpService->check($email, OtpType::PasswordReset, $code);

        if (! $isValid) {
            return response()->json([
                'message' => 'The password reset code is invalid or has expired.',
                'valid' => false,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => 'The password reset code is valid.',
            'valid' => true,
        ], Response::HTTP_OK);
    }

    /**
     * Reset the user's password using the verified OTP code.
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $email = strtolower(trim((string) $request->string('email')));
        $code = (string) $request->string('code');
        $newPassword = (string) $request->string('password');

        $isValid = $this->otpService->verify($email, OtpType::PasswordReset, $code);

        if (! $isValid) {
            return response()->json([
                'message' => 'The password reset code is invalid or has expired.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return response()->json([
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60),
        ])->save();

        // Revoke all existing tokens to secure account across devices
        $user->tokens()->delete();

        event(new PasswordReset($user));

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ], Response::HTTP_OK);
    }
}
