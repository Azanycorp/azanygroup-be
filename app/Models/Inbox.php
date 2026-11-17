<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InboxNote;

#[Fillable(['name', 'email', 'phone', 'subject', 'company_name', 'message', 'category', 'role', 'location', 'topic', 'reason', 'social_media_url', 'site_url', 'resume_url', 'job_id', 'job_title', 'status'])]

class Inbox extends Model
{
    /**
     * @return HasMany<InboxNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(InboxNote::class);
    }
}
