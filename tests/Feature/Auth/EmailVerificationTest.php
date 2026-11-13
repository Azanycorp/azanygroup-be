<?php

use App\Enums\OtpType;
use App\Models\User;
use App\Notifications\VerifyEmailOtpNotification;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('user can request email verification code', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'email' => 'unverified@example.com',
    ]);

    $response = $this->postJson(route('auth.verification.send'), [
        'email' => 'unverified@example.com',
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Verification code has been sent to your email.',
            'expires_in_minutes' => 10,
        ]);

    Notification::assertSentTo($user, VerifyEmailOtpNotification::class, function ($notification) {
        return strlen($notification->code) === 6 && ctype_digit($notification->code);
    });
});

test('authenticated user can request verification code without providing email', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('auth.verification.send'));

    $response->assertOk()
        ->assertJson([
            'message' => 'Verification code has been sent to your email.',
        ]);

    Notification::assertSentTo($user, VerifyEmailOtpNotification::class);
});

test('user cannot request verification code if already verified', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson(route('auth.verification.send'), [
        'email' => 'verified@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Email is already verified.',
        ]);

    Notification::assertNothingSent();
});

test('user cannot request verification code repeatedly within cooldown period', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'email' => 'repeat@example.com',
    ]);

    $this->postJson(route('auth.verification.send'), [
        'email' => 'repeat@example.com',
    ])->assertOk();

    $secondResponse = $this->postJson(route('auth.verification.send'), [
        'email' => 'repeat@example.com',
    ]);

    $secondResponse->assertStatus(429)
        ->assertJson([
            'message' => 'Please wait before requesting another verification code.',
        ]);
});

test('user can verify email with valid otp code', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'confirm@example.com',
    ]);

    $otpService = app(OtpService::class);
    $code = $otpService->generate('confirm@example.com', OtpType::EmailVerification, 10);

    $response = $this->postJson(route('auth.verification.verify'), [
        'email' => 'confirm@example.com',
        'code' => $code,
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Email verified successfully.',
            'user' => [
                'id' => $user->id,
                'email' => 'confirm@example.com',
                'is_email_verified' => true,
            ],
        ]);

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
});

test('user cannot verify email with invalid otp code', function () {
    User::factory()->unverified()->create([
        'email' => 'wrong@example.com',
    ]);

    $otpService = app(OtpService::class);
    $otpService->generate('wrong@example.com', OtpType::EmailVerification, 10);

    $response = $this->postJson(route('auth.verification.verify'), [
        'email' => 'wrong@example.com',
        'code' => '000000',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The verification code is invalid or has expired.',
        ]);
});

test('invalid attempts exceeding maximum invalidates the code', function () {
    User::factory()->unverified()->create([
        'email' => 'bruteforce@example.com',
    ]);

    $otpService = app(OtpService::class);
    $validCode = $otpService->generate('bruteforce@example.com', OtpType::EmailVerification, 10);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson(route('auth.verification.verify'), [
            'email' => 'bruteforce@example.com',
            'code' => '999999',
        ])->assertStatus(422);
    }

    // Now even the valid code should fail because it has been invalidated
    $response = $this->postJson(route('auth.verification.verify'), [
        'email' => 'bruteforce@example.com',
        'code' => $validCode,
    ]);

    $response->assertStatus(422);
});
