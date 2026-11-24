<?php

namespace App\Services;

use App\Http\Requests\NewCareerRequest;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use App\Traits\CreateActivityLog;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareerManagerService
{
    use CreateActivityLog,HttpResponses;

    public function allApplications(Request $request): JsonResponse
    {
        $query = Career::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = "%{$request->search}%";
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', $search)
                        ->orWhere('type', 'like', $search);
                });
            })
            ->when(
                $request->filled('status') && $request->input('status') !== 'all',
                function ($q) use ($request) {
                    $q->where('status', $request->input('status'));
                }
            );

        $inbox = $query->latest()->paginate($request->input('per_page', 10));

        return $this->paginatedResponse(CareerResource::collection($inbox), 'Career applications retrieved successfully.');
    }

    public function getApplicationDetails(int $id): JsonResponse
    {
        $career = Career::query()->where('id', $id)->first();
        if (! $career) {
            return $this->errorResponse(null, 'Career application not found.', 404);
        }

        return $this->successResponse(new CareerResource($career), 'Career details retrieved successfully.');
    }

    public function addNewApplication(NewCareerRequest $request): JsonResponse
    {
        Career::query()->create([
            'title' => $request->title,
            'team' => $request->team,
            'slug' => Str::slug($request->title),
            'venture' => $request->venture,
            'type' => $request->type,
            'work_mode' => $request->work_mode,
            'description' => $request->description,
            'location' => $request->location,
            'closing_date' => $request->closing_date,
            'is_active' => $request->is_active,
            'is_published' => $request->is_published,
        ]);

        $this->createLog(auth()->user(), 'Added new career application with ID: '.Career::query()->latest()->first()->id);

        return $this->successResponse(null, 'Career application added successfully.');
    }

    public function updateApplication(NewCareerRequest $request, int $id): JsonResponse
    {
        $career = Career::query()->where('id', $id)->first();
        if (! $career) {
            return $this->errorResponse(null, 'Career application not found.', 404);
        }
        $career->update([
            'title' => $request->title ?? $career->title,
            'team' => $request->team ?? $career->team,
            'slug' => Str::slug($request->title ?? $career->title),
            'venture' => $request->venture ?? $career->venture,
            'type' => $request->type ?? $career->type,
            'work_mode' => $request->work_mode ?? $career->work_mode,
            'description' => $request->description ?? $career->description,
            'location' => $request->location ?? $career->location,
            'closing_date' => $request->closing_date ?? $career->closing_date,
            'is_active' => $request->is_active ?? $career->is_active,
            'is_published' => $request->is_published ?? $career->is_published,
        ]);

        $this->createLog(auth()->user(), "Updated career application with ID: $id");

        return $this->successResponse(null, 'Career details updated successfully.');
    }

    public function deleteApplication(int $id): JsonResponse
    {
        $career = Career::query()->where('id', $id)->first();
        if (! $career) {
            return $this->errorResponse(null, 'Career application not found.', 404);
        }

        $this->createLog(auth()->user(), "Deleted career application with ID: $id");

        $career->delete();

        return $this->successResponse(null, 'Career application deleted successfully.');
    }
}
