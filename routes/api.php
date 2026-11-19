<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::post('contact-us', 'addContactMessage');
    Route::post('partner-with-us', 'storeInquiryMessage');
    Route::post('open-application', 'storeOpenApplication');
    Route::post('newsletter', 'storeNewsletterSubscription');
    Route::post('job-application', 'storeJobApplication');
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
        Route::get('inbox-messages/{id}', 'getInboxMessage');
        Route::put('inbox-messages/{id}', 'updateInboxMessage');
        Route::post('inbox-messages/{id}/notes', 'addNote');
    });

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('', 'profile');

        Route::post('logout', 'logout');
    });
});
