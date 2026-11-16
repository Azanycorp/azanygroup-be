<?php

namespace App\Services;

use App\Models\Inbox;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ContactUsRequest;
use App\Enum\InboxCategory;
use App\Http\Requests\InquiryRequest;
use App\Http\Requests\OpenApplicationRequest;

class HomeService
{
    use HttpResponses;
    public function addContactMessage(ContactUsRequest $request): JsonResponse
    {
        Inbox::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'category' => InboxCategory::CONTACT_US->value,
            'email' => $request->email,
            'topic' => $request->subject,
            'company_name' => $request->company_name,
            'message' => $request->message
        ]);
        return $this->successResponse(null, 'Contact message sent successfully.');
    }

    public function storeInquiryMessage(InquiryRequest $request): JsonResponse
    {
        Inbox::create([
            'role' => $request->role,
            'name' => $request->name,
            'site_url' => $request->site_url,
            'category' => InboxCategory::INQUIRY->value,
            'email' => $request->email,
            'reason' => $request->reason,
            'company_name' => $request->company_name,
            'message' => $request->message
        ]);
        return $this->successResponse(null, 'Inquiry message sent successfully.');
    }

    public function storeOpenApplication(OpenApplicationRequest $request): JsonResponse
    {
         $resume_url = $request->hasFile('resume') ? upload_image($request->file('resume'), 'resumes') : null;

        Inbox::create([
            'role' => $request->role,
            'name' => $request->name,
            'social_media_url' => $request->social_media_url,
            'category' => InboxCategory::APPLICATION->value,
            'email' => $request->email,
            'resume_url' => $resume_url,
            'message' => $request->message
        ]);
        return $this->successResponse(null, 'Open application submitted successfully.');
    }
    
}
