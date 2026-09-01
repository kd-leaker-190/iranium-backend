<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TicketPriority: string implements HasLabel
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => 'پایین',
            self::MEDIUM => 'متوسط',
            self::HIGH => 'بالا'
        };
    }
}
