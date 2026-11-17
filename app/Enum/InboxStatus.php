<?php

namespace App\Enum;
enum InboxStatus: string
{
    case NEW = 'new';
    case READ = 'read';
    case HANDLED = 'handled';
}
