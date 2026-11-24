<?php

namespace App\Services;

use App\Enum\InboxCategory;
use App\Enum\InboxStatus;
use App\Http\Requests\AddNoteRequest;
use App\Http\Requests\UpdateInboxRequest;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\InboxResource;
use App\Models\ActivityLog;
use App\Models\Blog;
use App\Models\Career;
use App\Models\Inbox;
use App\Models\NewsLetter;
use App\Traits\CreateActivityLog;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminService
{
    use CreateActivityLog, HttpResponses;

    public function searchInbox(Request $request): JsonResponse
    {
        $messages = Inbox::query()->when($request->search, function ($q) use ($request) {
            $search = "%{$request->search}%";
            $q->where('name', 'like', $search);
            $q->orWhere('email', 'like', $search);
        })->get();

        $search = InboxResource::collection($messages);

        return $this->successResponse($search, 'Searches found');
    }

    public function dashboard(): JsonResponse
    {
        $messages = Inbox::query()->where('status', InboxStatus::NEW->value)->count();
        $publishedBlogs = Blog::query()->where('is_published', true)->count();
        $unpublishedBlogs = Blog::query()->where('is_published', false)->count();

        $publishedCareers = Career::query()->where('is_published', true)->count();
        $unpublishedCareers = Career::query()->where('is_published', false)->count();

        $activities = ActivityLogResource::Collection(ActivityLog::query()->latest()->take(10)->get());

        $recentBlogs = BlogResource::Collection(Blog::query()->where('is_published', false)->latest()->take(10)->get());

        $data = [
            'new_messages' => $messages,
            'recent_blogs' => $recentBlogs,
            'published' => $publishedBlogs + $publishedCareers,
            'unpublished' => $unpublishedBlogs + $unpublishedCareers,
            'recent_activities' => $activities,
        ];

        return $this->successResponse($data, 'Dashboard data retrieved successfully.');
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
                        ->orWhere('email', 'like', $search)
                        ->orWhere('category', 'like', $search);
                });
            })
            ->when(
                $request->filled('status') && $request->input('status') !== 'all',
                function ($q) use ($request) {
                    $q->where('status', $request->input('status'));
                }
            )
            ->when(
                $request->filled('category') && $request->input('category') !== 'all',
                function ($q) use ($request) {
                    $q->where('category', $request->input('category'));
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
        $inbox->update(['status' => $request->status]);
        $this->createLog(auth()->user(), "Updated inbox message with ID: $id to status 'read'");

        return $this->successResponse(null, 'Inbox message updated successfully.');
    }

    public function addNote(AddNoteRequest $request, int $id): JsonResponse
    {
        $inbox = Inbox::query()->where('id', $id)->first();

        if (! $inbox) {
            return $this->errorResponse(null, 'Inbox message not found.', 404);
        }

        $inbox->notes()->create(['note' => $request->note]);
        $this->createLog(auth()->user(), "Added note to inbox message with ID: $id");

        return $this->successResponse(null, 'Note added successfully.');
    }
}
