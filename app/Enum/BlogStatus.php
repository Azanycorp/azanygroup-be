<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum BlogStatus: string
{
    case NEW = 'new';
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public static function values(): mixed
    {
        return new Collection(self::cases())->pluck('value');
    }
}
