<?php

namespace App\Enums;


enum Status: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';

    public static function toArray(): array
    {
        return array_column(Status::cases(), 'value');
    }
}
