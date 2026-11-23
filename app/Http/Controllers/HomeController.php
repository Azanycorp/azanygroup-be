<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use App\Http\Requests\InquiryRequest;
use App\Http\Requests\JobApplicationRequest;
use App\Http\Requests\NewsletterRequest;
use App\Http\Requests\OpenApplicationRequest;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService
    ) {}

    public function migrate(): JsonResponse
    {
        return $this->homeService->migrate();
    }

    public function addContactMessage(ContactUsRequest $request): JsonResponse
    {
        return $this->homeService->addContactMessage($request);
    }

    public function storeInquiryMessage(InquiryRequest $request): JsonResponse
    {
        return $this->homeService->storeInquiryMessage($request);
    }

    public function storeJobApplication(JobApplicationRequest $request): JsonResponse
    {
        return $this->homeService->storeJobApplication($request);
    }

    public function storeOpenApplication(OpenApplicationRequest $request): JsonResponse
    {
        return $this->homeService->storeOpenApplication($request);
    }

    public function storeNewsletterSubscription(NewsletterRequest $request): JsonResponse
    {
        return $this->homeService->storeNewsletterSubscription($request);
    }
}
