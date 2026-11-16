<?php
namespace App\Services;
use App\Http\Requests\VerifyUserRequest;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserResetPassRequest;
use App\Http\Requests\CodeRequest;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;

class AccountService
{
    use HttpResponses;
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $token = $user->createToken($request->input('email'))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Login successful.');
    }

    public function resendCode($request): JsonResponse
    {
        $email = Auth::check() ? Auth::user()->email : $request->email;

        $user = User::where('email', $email)->first();

        if (! $user) {
            return $this->errorResponse(null, 'Oops! No record found with your entry.', 404);
        }

        $verificationCode = generate_user_verification_code();
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new PasswordResetCodeMail($user));

        return $this->successResponse(null, 'A new code has been sent to you');
    }

    public function verifyUserIdentity(VerifyUserRequest $request): JsonResponse
    {
        $user = User::where('email',$request->email)->first();

        if (! $user) {
            return $this->errorResponse(null,'Oops! No record found with your entry.', 404);
        }

        $verificationCode = generate_user_verification_code();
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => now()->addMinutes(10),
        ]);

       Mail::to($user->email)->send(new PasswordResetCodeMail($user));

        return $this->successResponse(null, 'A verification code has been sent to your email');
    }

    public function verifyCode(CodeRequest $request): JsonResponse
    {
        $user = User::where('verification_code', $request->verification_code)->first();
        if (! $user) {
            return $this->errorResponse(null, 'Invalid code entered, please try it again.', 400);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse(null, 'Verification Code has Expired!', 400);
        }
        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->successResponse(null, 'Code Verified');
    }

    public function changePassword(UserResetPassRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse(null, 'Password Reset successfully!');
    }

}