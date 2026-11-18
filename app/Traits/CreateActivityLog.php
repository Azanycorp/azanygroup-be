<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Jenssegers\Agent\Facades\Agent;

trait CreateActivityLog
{
    public function createLog($user,$action): void
    {
        $browser = Agent::browser();
        $os = Agent::platform();
        ActivityLog::create([
            'user' => $user,
            'action' => $action,
            'os' => $os,
            'browser' => $browser,
            'ip_address' => request()->ip(),
        ]);
    }
}
