<?php

use App\Http\Controllers\Api\V1\Auth\AccountController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProfileController;

Route::prefix('auth')->name('auth.')->group(function (): void {
    // Authentication

    Route::controller(AccountController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('send', 'verifyUser')->name('send');
        Route::post('verify', 'verifyCode')->name('verify');
        Route::post('resend-code', 'resendCode')->name('resend-code');
        Route::post('reset-password', 'changePassword')->name('reset');
    });
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function (): void {
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('', 'profile')->name('me');


        Route::post('logout', 'logout')->name('logout');
    });
});
