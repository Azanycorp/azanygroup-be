<?php

use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->name('auth.')->group(function (): void {
    // Authentication
    Route::post('login', [LoginController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('me', [LoginController::class, 'me'])->name('me');
    });

    // Email Verification (6-digit OTP)
    Route::prefix('verification')->name('verification.')->group(function (): void {
        Route::post('send', [EmailVerificationController::class, 'send'])->name('send');
        Route::post('verify', [EmailVerificationController::class, 'verify'])->name('verify');
    });

    // Password Reset (6-digit OTP)
    Route::prefix('password')->name('password.')->group(function (): void {
        Route::post('forgot', [PasswordResetController::class, 'forgot'])->name('forgot');
        Route::post('verify-code', [PasswordResetController::class, 'verifyCode'])->name('verify-code');
        Route::post('reset', [PasswordResetController::class, 'reset'])->name('reset');
    });
});
