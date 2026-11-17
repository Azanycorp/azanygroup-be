<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Container\Attributes\CurrentUser;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {
    }
    public function profile(#[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->profile($user);
    }
    
    public function logout(#[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->logout($user);
    }
}