<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendEmailVerificationOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyEmailOtpNotification;
use App\Services\OtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EmailVerificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public OtpService $otpService,
    ) {}

    /**
     * Send or resend an email verification OTP code.
     */
    public function send(SendEmailVerificationOtpRequest $request): JsonResponse
    {
        $email = $request->targetEmail();
        $user = User::where('email', $email)->first();

        if ($user === null) {
            return response()->json([
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email is already verified.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->otpService->isThrottled($email, OtpType::EmailVerification)) {
            return response()->json([
                'message' => 'Please wait before requesting another verification code.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $code = $this->otpService->generate($email, OtpType::EmailVerification, 10);
        $user->notify(new VerifyEmailOtpNotification($code, 10));

        return response()->json([
            'message' => 'Verification code has been sent to your email.',
            'expires_in_minutes' => 10,
        ], Response::HTTP_OK);
    }

    /**
     * Verify the user's email address using the provided OTP code.
     */
    public function verify(VerifyEmailOtpRequest $request): JsonResponse
    {
        $email = $request->targetEmail();
        $user = User::where('email', $email)->first();

        if ($user === null) {
            return response()->json([
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email is already verified.',
                'user' => new UserResource($user),
            ], Response::HTTP_OK);
        }

        $isValid = $this->otpService->verify(
            $email,
            OtpType::EmailVerification,
            (string) $request->string('code'),
        );

        if (! $isValid) {
            return response()->json([
                'message' => 'The verification code is invalid or has expired.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            'message' => 'Email verified successfully.',
            'user' => new UserResource($user->fresh()),
        ], Response::HTTP_OK);
    }
}
