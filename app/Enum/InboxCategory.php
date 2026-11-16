<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum InboxCategory: string
{
    case CONTACT_US = 'contact_us';
    case APPLICATION = 'application';
    case INQUIRY = 'inquiry';


}
