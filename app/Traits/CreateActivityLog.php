<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Jenssegers\Agent\Facades\Agent;
use App\Models\User;

trait CreateActivityLog
{
    public function createLog(User $user, mixed $action): void
    {
        $browser = Agent::browser();
        $os = Agent::platform();
        ActivityLog::query()->create([
            'user' => $user->name,
            'action' => $action,
            'os' => $os,
            'browser' => $browser,
            'ip_address' => request()->ip(),
        ]);
    }
}
