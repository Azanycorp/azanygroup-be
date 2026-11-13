<?php

use App\Http\Controllers\Api\V1\Auth\AccountController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function (): void {
    // Authentication
    Route::post('login', [LoginController::class, 'login'])->name('login');  

    // Account: Email Verification (6-digit OTP)
      Route::controller(AccountController::class)->group(function () {
        Route::post('send','sendVerificationCode')->name('send');
        Route::post('verify','verifyEmail')->name('verify');
    });

    // Account: Password Reset (6-digit OTP)
    Route::controller(AccountController::class)->group(function () {
        Route::post('forgot', 'forgotPassword')->name('forgot');
        Route::post('verify-code', 'verifyPasswordResetCode')->name('verify-code');
        Route::post('reset', 'resetPassword')->name('reset');
    });
});

 Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('me', [LoginController::class, 'me'])->name('me');
    });
