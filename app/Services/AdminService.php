<?php
namespace App\Services;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class AdminService
{
    use HttpResponses;
    public function dashboard(): JsonResponse
    {
        return $this->successResponse(null, 'Dashboard data retrieved successfully.');
    }

}