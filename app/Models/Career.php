<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[fillable([
    'title',
    'slug',
    'team',
    'venture',
    'type',
    'work_mode',
    'description',
    'location',
    'closing_date',
    'is_active',
    'is_published'
])]
class Career extends Model
{

}
