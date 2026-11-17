<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService
    ) {
    }
    public function dashboard(): JsonResponse
    {
        return $this->adminService->dashboard();
    }
    public function inboxMessages(Request $request): JsonResponse
    {
        return $this->adminService->inboxMessages($request);
    }
    
}