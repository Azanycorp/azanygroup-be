<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;
use Jenssegers\Agent\Facades\Agent;

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
