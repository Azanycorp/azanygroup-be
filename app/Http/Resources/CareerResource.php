<?php

namespace App\Http\Resources;

use App\Models\Career;
use App\Models\InboxNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Career
 */
class CareerResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'team' => $this->team,
            'venture' => $this->venture,
            'type' => $this->type,
            'work_mode' => $this->work_mode,
            'description' => $this->description,
            'location' => $this->location,
            'closing_date' => $this->closing_date,
            'is_active' => $this->is_active,
            'is_published' => $this->is_published
        ];
    }
}
