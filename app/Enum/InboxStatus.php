<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum InboxStatus: string
{
    case NEW = 'new';
    case READ = 'read';
    case HANDLED = 'handled';
}
