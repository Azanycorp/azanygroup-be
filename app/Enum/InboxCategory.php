<?php

namespace App\Enum;
enum InboxCategory: string
{
    case CONTACT_US = 'contact_us';
    case APPLICATION = 'application';
    case INQUIRY = 'inquiry';
    case NEWSLETTER = 'newsletter';

}
