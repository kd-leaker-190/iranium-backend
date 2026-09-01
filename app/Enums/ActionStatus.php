<?php

namespace App\Enums;


use Modules\Support\Traits\CleanEnum;

enum ActionStatus: string
{
    use CleanEnum;
    case Pending = 'Pending';
    case Completed = 'Completed';
    case Timeout = 'Timeout';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'در حال انجام',
            self::Completed => 'انجام شده',
            self::Timeout => 'زمان تمام شده',
        };
    }
}
