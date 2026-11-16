<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\AdminService;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {
    }
    public function dashboard(): JsonResponse
    {
        return $this->adminService->dashboard();
    }
    
}