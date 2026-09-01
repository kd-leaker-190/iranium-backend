<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasLabel
{
    case WAITING_FOR_ADMIN = 'waiting_for_admin';
    case WAITING_FOR_TEAM = 'waiting_for_team';
    case CLOSED = 'closed';
    public function getLabel(): string
    {
        return match ($this) {
            self::WAITING_FOR_ADMIN => 'در انتظار پاسخ مدیریت',
            self::WAITING_FOR_TEAM => 'در انتظار پاسخ تیم',
            self::CLOSED => 'بسته شده'
        };
    }
}
