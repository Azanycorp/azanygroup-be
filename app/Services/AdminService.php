<?php

namespace App\Services;

use App\Enum\InboxCategory;
use App\Enum\InboxStatus;
use App\Http\Requests\AddNoteRequest;
use App\Http\Requests\UpdateInboxRequest;
use App\Http\Resources\InboxResource;
use App\Models\Inbox;
use App\Models\NewsLetter;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminService
{
    use HttpResponses;

    public function dashboard(): JsonResponse
    {
        $messages = Inbox::query()->where('status', InboxStatus::NEW->value)->count();
        $published = Blog::query()->where('status', BlogStatus::PUBLISHED->value)->count();
        
        return $this->successResponse(null, 'Dashboard data retrieved successfully.');
    }

    public function inboxMessages(Request $request): JsonResponse
    {
        $counts = Inbox::query()->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');
        $newsLetters = NewsLetter::query()->count();

        $query = Inbox::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = "%{$request->search}%";
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            })
            ->when(
                $request->filled('status') && $request->input('status') !== 'all',
                function ($q) use ($request) {
                    $q->where('status', $request->input('status'));
                }
            );

        $inbox = $query->latest()->paginate($request->input('per_page', 10));

        $data = [
            'all_forms' => $counts->sum(),
            'news_letters' => $newsLetters,
            'applications' => $counts->get(InboxCategory::APPLICATION->value, 0),
            'inquiries' => $counts->get(InboxCategory::INQUIRY->value, 0),
            'contact_us' => $counts->get(InboxCategory::CONTACT_US->value, 0),
            'inbox' => InboxResource::collection($inbox),
        ];

        return $this->successResponse($data, 'Inbox messages retrieved successfully.');
    }

    public function getInboxMessage(int $id): JsonResponse
    {
        $inbox = Inbox::with('notes')->where('id', $id)->first();
        if (! $inbox) {
            return $this->errorResponse(null, 'Inbox message not found.', 404);
        }

        return $this->successResponse(new InboxResource($inbox), 'Inbox message retrieved successfully.');
    }

    public function updateInboxMessage(UpdateInboxRequest $request, int $id): JsonResponse
    {
        $inbox = Inbox::query()->where('id', $id)->first();
        if (! $inbox) {
            return $this->errorResponse(null, 'Inbox message not found.', 404);
        }
        $inbox->update(['status' => 'read']);

        return $this->successResponse(null, 'Inbox message updated successfully.');
    }

    public function addNote(AddNoteRequest $request, int $id): JsonResponse
    {
        $inbox = Inbox::query()->where('id', $id)->first();

        if (! $inbox) {
            return $this->errorResponse(null, 'Inbox message not found.', 404);
        }

        $inbox->notes()->create(['note' => $request->note]);

        return $this->successResponse(null, 'Note added successfully.');
    }
}
