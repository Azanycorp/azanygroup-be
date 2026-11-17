<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum InboxStatus: string
{
    case NEW = 'new';
    case READ = 'read';
    case HANDLED = 'handled';

      public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
