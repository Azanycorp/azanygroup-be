<?php
namespace App\Services;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enum\InboxCategory;
use App\Models\Inbox;
use App\Http\Resources\InboxResource;

class AdminService
{
    use HttpResponses;
    public function dashboard(): JsonResponse
    {
        return $this->successResponse(null, 'Dashboard data retrieved successfully.');
    }
    public function inboxMessages(Request $request): JsonResponse
    {
       $counts = Inbox::query()->selectRaw('category, COUNT(*) as total')
             ->groupBy('category')
             ->pluck('total', 'category');

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
            'applications' => $counts->get(InboxCategory::APPLICATION->value, 0),
            'inquiries' => $counts->get(InboxCategory::INQUIRY->value, 0),
            'contact_us' => $counts->get(InboxCategory::CONTACT_US->value, 0),
            'news_letters' => $counts->get(InboxCategory::NEWSLETTER->value, 0),
            'inbox' => InboxResource::collection($inbox),
        ];

        return $this->successResponse($data, 'Inbox messages retrieved successfully.');
    }

}