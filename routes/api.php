<?php

use App\Http\Controllers\Api\V1\Auth\AccountController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->name('auth.')->group(function (): void {
    // Authentication
    Route::post('login', [LoginController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('me', [LoginController::class, 'me'])->name('me');
    });

    // Account: Email Verification (6-digit OTP)
    Route::prefix('verification')->name('verification.')->group(function (): void {
        Route::post('send', [AccountController::class, 'sendVerificationCode'])->name('send');
        Route::post('verify', [AccountController::class, 'verifyEmail'])->name('verify');
    });

    // Account: Password Reset (6-digit OTP)
    Route::prefix('password')->name('password.')->group(function (): void {
        Route::post('forgot', [AccountController::class, 'forgotPassword'])->name('forgot');
        Route::post('verify-code', [AccountController::class, 'verifyPasswordResetCode'])->name('verify-code');
        Route::post('reset', [AccountController::class, 'resetPassword'])->name('reset');
    });
});
