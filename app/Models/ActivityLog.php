<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('user', 'action', 'os', 'browser', 'ip_address')]
class ActivityLog extends Model
{
    //
}
