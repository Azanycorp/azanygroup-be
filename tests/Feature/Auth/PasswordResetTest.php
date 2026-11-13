<?php

use App\Enums\OtpType;
use App\Models\User;
use App\Notifications\ResetPasswordOtpNotification;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('forgot password returns success message for non-registered email', function () {
    Notification::fake();

    $response = $this->postJson(route('auth.password.forgot'), [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'If your email is registered in our system, you will receive a password reset code.',
            'expires_in_minutes' => 15,
        ]);

    Notification::assertNothingSent();
});

test('forgot password sends otp notification to registered user', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->postJson(route('auth.password.forgot'), [
        'email' => 'user@example.com',
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'If your email is registered in our system, you will receive a password reset code.',
        ]);

    Notification::assertSentTo($user, ResetPasswordOtpNotification::class, function ($notification) {
        return strlen($notification->code) === 6 && ctype_digit($notification->code);
    });
});

test('verify-code endpoint confirms valid code without consuming it', function () {
    User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $otpService = app(OtpService::class);
    $code = $otpService->generate('user@example.com', OtpType::PasswordReset, 15);

    $response = $this->postJson(route('auth.password.verify-code'), [
        'email' => 'user@example.com',
        'code' => $code,
    ]);

    $response->assertOk()
        ->assertJson([
            'valid' => true,
            'message' => 'The password reset code is valid.',
        ]);

    // Ensure the code still exists and can be verified again
    expect($otpService->hasActiveOtp('user@example.com', OtpType::PasswordReset))->toBeTrue();
});

test('verify-code endpoint rejects invalid code', function () {
    User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $otpService = app(OtpService::class);
    $otpService->generate('user@example.com', OtpType::PasswordReset, 15);

    $response = $this->postJson(route('auth.password.verify-code'), [
        'email' => 'user@example.com',
        'code' => '111111',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'valid' => false,
            'message' => 'The password reset code is invalid or has expired.',
        ]);
});

test('user can reset password with valid otp code and new password', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('old-password-123'),
    ]);

    // Create existing access token to verify revocation
    $user->createToken('old-phone-session');
    expect($user->tokens()->count())->toBe(1);

    $otpService = app(OtpService::class);
    $code = $otpService->generate('user@example.com', OtpType::PasswordReset, 15);

    $response = $this->postJson(route('auth.password.reset'), [
        'email' => 'user@example.com',
        'code' => $code,
        'password' => 'New-Secret-Password-123',
        'password_confirmation' => 'New-Secret-Password-123',
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Password has been reset successfully.',
        ]);

    // Verify existing tokens were revoked
    expect($user->tokens()->count())->toBe(0);

    // Verify OTP code is consumed
    expect($otpService->hasActiveOtp('user@example.com', OtpType::PasswordReset))->toBeFalse();

    // Verify user can now log in with the new password
    $loginResponse = $this->postJson(route('auth.login'), [
        'email' => 'user@example.com',
        'password' => 'New-Secret-Password-123',
    ]);

    $loginResponse->assertOk()
        ->assertJsonStructure(['access_token']);
});

test('user cannot reset password with mismatched password confirmation', function () {
    User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $otpService = app(OtpService::class);
    $code = $otpService->generate('user@example.com', OtpType::PasswordReset, 15);

    $response = $this->postJson(route('auth.password.reset'), [
        'email' => 'user@example.com',
        'code' => $code,
        'password' => 'new-password-123',
        'password_confirmation' => 'mismatch-123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('user cannot reset password with invalid code', function () {
    User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->postJson(route('auth.password.reset'), [
        'email' => 'user@example.com',
        'code' => '999999',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The password reset code is invalid or has expired.',
        ]);
});
