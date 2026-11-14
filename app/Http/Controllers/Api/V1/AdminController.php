<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Container\Attributes\CurrentUser;
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
