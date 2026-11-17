<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use App\Http\Requests\ContactUsRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InquiryRequest;
use App\Http\Requests\OpenApplicationRequest;
use App\Http\Requests\NewsletterRequest;

class HomeController extends Controller
{
     public function __construct(
        private readonly HomeService $homeService
    ) {
    }
    public function addContactMessage(ContactUsRequest $request): JsonResponse
    {
        return $this->homeService->addContactMessage($request);
    }
    public function storeInquiryMessage(InquiryRequest $request): JsonResponse
    {
        return $this->homeService->storeInquiryMessage($request);
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
