<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\ResetPasswordOtpNotification;
use App\Notifications\VerifyEmailOtpNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        public OtpService $otpService,
    ) {}

    /**
     * Send or resend an email verification OTP code.
     *
     * @return array{success: bool, message: string, status: int, data?: array<string, mixed>, errors?: array<string, string>}
     */
    public function sendVerificationCode(string $email): array
    {
        $normalizedEmail = strtolower(trim($email));
        $user = User::where('email', $normalizedEmail)->first();

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => ['email' => 'User not found.'],
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email is already verified.',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => ['email' => 'Email is already verified.'],
            ];
        }

        if ($this->otpService->isThrottled($normalizedEmail, OtpType::EmailVerification)) {
            return [
                'success' => false,
                'message' => 'Please wait before requesting another verification code.',
                'status' => Response::HTTP_TOO_MANY_REQUESTS,
                'errors' => ['throttle' => 'Please wait before requesting another verification code.'],
            ];
        }

        $code = $this->otpService->generate($normalizedEmail, OtpType::EmailVerification, 10);
        $user->notify(new VerifyEmailOtpNotification($code, 10));

        return [
            'success' => true,
            'message' => 'Verification code has been sent to your email.',
            'status' => Response::HTTP_OK,
            'data' => [
                'expires_in_minutes' => 10,
            ],
        ];
    }

    /**
     * Verify the user's email address using the provided OTP code.
     *
     * @return array{success: bool, message: string, status: int, data?: array<string, mixed>, errors?: array<string, string>}
     */
    public function verifyEmail(string $email, string $code): array
    {
        $normalizedEmail = strtolower(trim($email));
        $user = User::where('email', $normalizedEmail)->first();

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => ['email' => 'User not found.'],
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => true,
                'message' => 'Email is already verified.',
                'status' => Response::HTTP_OK,
                'data' => [
                    'user' => new UserResource($user),
                ],
            ];
        }

        $isValid = $this->otpService->verify(
            $normalizedEmail,
            OtpType::EmailVerification,
            $code,
        );

        if (! $isValid) {
            return [
                'success' => false,
                'message' => 'The verification code is invalid or has expired.',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => ['code' => 'The verification code is invalid or has expired.'],
            ];
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return [
            'success' => true,
            'message' => 'Email verified successfully.',
            'status' => Response::HTTP_OK,
            'data' => [
                'user' => new UserResource($user->fresh()),
            ],
        ];
    }

    /**
     * Send a 6-digit password reset OTP to the user's email.
     *
     * @return array{success: bool, message: string, status: int, data?: array<string, mixed>, errors?: array<string, string>}
     */
    public function sendPasswordResetCode(string $email): array
    {
        $normalizedEmail = strtolower(trim($email));
        $user = User::where('email', $normalizedEmail)->first();

        if ($user !== null && ! $this->otpService->isThrottled($normalizedEmail, OtpType::PasswordReset)) {
            $code = $this->otpService->generate($normalizedEmail, OtpType::PasswordReset, 15);
            $user->notify(new ResetPasswordOtpNotification($code, 15));
        }

        return [
            'success' => true,
            'message' => 'If your email is registered in our system, you will receive a password reset code.',
            'status' => Response::HTTP_OK,
            'data' => [
                'expires_in_minutes' => 15,
            ],
        ];
    }

    /**
     * Verify whether a password reset OTP code is valid before prompting the new password.
     *
     * @return array{success: bool, message: string, status: int, data?: array<string, mixed>, errors?: array<string, string>}
     */
    public function verifyPasswordResetCode(string $email, string $code): array
    {
        $normalizedEmail = strtolower(trim($email));

        $isValid = $this->otpService->check($normalizedEmail, OtpType::PasswordReset, $code);

        if (! $isValid) {
            return [
                'success' => false,
                'message' => 'The password reset code is invalid or has expired.',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => ['code' => 'The password reset code is invalid or has expired.'],
                'data' => ['valid' => false],
            ];
        }

        return [
            'success' => true,
            'message' => 'The password reset code is valid.',
            'status' => Response::HTTP_OK,
            'data' => ['valid' => true],
        ];
    }

    /**
     * Reset the user's password using the verified OTP code.
     *
     * @return array{success: bool, message: string, status: int, data?: array<string, mixed>, errors?: array<string, string>}
     */
    public function resetPassword(string $email, string $code, string $newPassword): array
    {
        $normalizedEmail = strtolower(trim($email));

        $isValid = $this->otpService->verify($normalizedEmail, OtpType::PasswordReset, $code);

        if (! $isValid) {
            return [
                'success' => false,
                'message' => 'The password reset code is invalid or has expired.',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => ['code' => 'The password reset code is invalid or has expired.'],
            ];
        }

        $user = User::where('email', $normalizedEmail)->first();

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'User not found.',
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => ['email' => 'User not found.'],
            ];
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60),
        ])->save();

        $user->tokens()->delete();

        event(new PasswordReset($user));

        return [
            'success' => true,
            'message' => 'Password has been reset successfully.',
            'status' => Response::HTTP_OK,
            'data' => null,
        ];
    }
}
