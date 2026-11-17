<?php

namespace App\Http\Resources;

use App\Models\Inbox;
use App\Models\InboxNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Inbox
 */
class InboxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'job_title' => $this->job_title,
            'company_name' => $this->company_name,
            'category' => $this->category,
            'status' => $this->status,
            'phone' => $this->phone,
            'location' => $this->location,
            'topic' => $this->topic,
            'reason' => $this->reason,
            'social_media_url' => $this->social_media_url,
            'site_url' => $this->site_url,
            'resume_url' => $this->resume_url,
            'message' => $this->message,
            'date' => $this->created_at?->diffForHumans(),
            'notes' => $this->whenLoaded('notes', fn () => $this->notes->map(fn (InboxNote $note): array => [
                'id' => $note->id,
                'note' => $note->note,
                'date' => $note->created_at?->diffForHumans(),
            ])),
        ];
    }
}
