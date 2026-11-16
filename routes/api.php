<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;

Route::controller(HomeController::class)->group(function () {
    Route::post('contact-us', 'addContactMessage');
    Route::post('inquiry', 'storeInquiryMessage');
    Route::post('open-application', 'storeOpenApplication');
});

Route::prefix('auth')->group(function (): void {
    Route::controller(AccountController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('send', 'verifyUser');
        Route::post('verify', 'verifyCode');
        Route::post('resend-code', 'resendCode');
        Route::post('reset-password', 'changePassword');
    });
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function (): void {
    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'dashboard');
        Route::get('inbox-messages', 'inboxMessages');
    });

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('', 'profile');

        Route::post('logout', 'logout');
    });
});