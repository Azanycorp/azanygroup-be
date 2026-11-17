<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddNoteRequest;
use App\Http\Requests\UpdateInboxRequest;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService
    ) {}

    public function dashboard(): JsonResponse
    {
        return $this->adminService->dashboard();
    }

    public function inboxMessages(Request $request): JsonResponse
    {
        return $this->adminService->inboxMessages($request);
    }

    public function getInboxMessage(int $id): JsonResponse
    {
        return $this->adminService->getInboxMessage($id);
    }

    public function updateInboxMessage(UpdateInboxRequest $request, int $id): JsonResponse
    {
        return $this->adminService->updateInboxMessage($request, $id);
    }

    public function addNote(AddNoteRequest $request, int $id): JsonResponse
    {
        return $this->adminService->addNote($request, $id);
    }
}
