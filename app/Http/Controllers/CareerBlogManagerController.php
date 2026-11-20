<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\BlogManagerService;
use App\Http\Requests\NewBlogRequest;
use Illuminate\Container\Attributes\CurrentUser;
use App\Services\CareerManagerService;
use App\Http\Requests\NewCareerRequest;

class CareerBlogManagerController extends Controller
{
    public function __construct(
        private readonly CareerManagerService $careerManagerService,
        private readonly BlogManagerService $blogManagerService
    ) {}

    public function allApplications(Request $request): JsonResponse
    {
        return $this->careerManagerService->allApplications($request);
    }

    public function getApplicationDetails(int $id): JsonResponse
    {
        return $this->careerManagerService->getApplicationDetails($id);
    }

    public function addNewApplication(NewCareerRequest $request): JsonResponse
    {
        return $this->careerManagerService->addNewApplication($request);
    }

    public function updateApplication(NewCareerRequest $request, #[CurrentUser()] $user): JsonResponse
    {
        return $this->careerManagerService->updateApplication($request, $user);
    }

    public function deleteApplication(int $id): JsonResponse
    {
        return $this->careerManagerService->deleteApplication($id);
    }

    //Blog section

    
    public function allBlogs(Request $request): JsonResponse
    {
        return $this->blogManagerService->allBlogs($request);
    }

    public function getBlogDetails(int $id): JsonResponse
    {
        return $this->blogManagerService->getBlogDetails($id);
    }

    public function updateBlog(NewBlogRequest $request, int $id): JsonResponse
    {
        return $this->blogManagerService->updateBlog($request, $id);
    }

    public function addNewBlog(NewBlogRequest $request, #[CurrentUser()] $user): JsonResponse
    {
        return $this->blogManagerService->addNewBlog($request, $user);
    }

    public function deleteBlog(int $id): JsonResponse
    {
        return $this->blogManagerService->deleteBlog($id);
    }
}
