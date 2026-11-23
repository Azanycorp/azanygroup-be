<?php

namespace App\Services;

use App\Http\Requests\NewBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\User;
use App\Traits\CreateActivityLog;
use App\Traits\HttpResponses;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogManagerService
{
    use CreateActivityLog,HttpResponses;

    public function allBlogs(Request $request): JsonResponse
    {
        $query = Blog::query()
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

        return $this->paginatedResponse(BlogResource::collection($inbox), 'Blog posts retrieved successfully.');
    }

    public function getBlogDetails(int $id): JsonResponse
    {
        $blog = Blog::query()->where('id', $id)->first();
        if (! $blog) {
            return $this->errorResponse(null, 'Blog post not found.', 404);
        }

        return $this->successResponse(new BlogResource($blog), 'Blog details retrieved successfully.');
    }

    public function addNewBlog(NewBlogRequest $request, #[CurrentUser()] User $user): JsonResponse
    {
        Blog::query()->create([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'author' => $user->name,
            'slug' => Str::slug($request->title),
            'content' => $request->long_description,
            'category' => $request->category,
            'is_published' => $request->is_published,
        ]);
        $this->createLog($user, 'Added new blog post with ID: '.Blog::query()->latest()->first()->id);

        return $this->successResponse(null, 'Blog post added successfully.');
    }

    public function updateBlog(NewBlogRequest $request, int $id): JsonResponse
    {
        $blog = Blog::query()->where('id', $id)->first();
        if (! $blog) {
            return $this->errorResponse(null, 'Blog post not found.', 404);
        }
        $blog->update([
            'title' => $request->title ?? $blog->title,
            'short_description' => $request->short_description ?? $blog->short_description,
            'slug' => Str::slug($request->title ?? $blog->title),
            'content' => $request->long_description ?? $blog->content,
            'category' => $request->category ?? $blog->category,
            'is_published' => $request->is_published ?? $blog->is_published,
        ]);

        $this->createLog(auth()->user(), "Updated blog post with ID: $id");

        return $this->successResponse(null, 'Blog post details updated successfully.');
    }

    public function deleteBlog(int $id): JsonResponse
    {
        $blog = Blog::query()->where('id', $id)->first();
        if (! $blog) {
            return $this->errorResponse(null, 'Blog post not found.', 404);
        }

        $this->createLog(auth()->user(), "Deleted blog post with ID: $id");

        $blog->delete();

        return $this->successResponse(null, 'Blog post deleted successfully.');
    }
}
