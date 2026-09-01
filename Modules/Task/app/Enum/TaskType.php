<?php

namespace Modules\Task\Enum;

use Filament\Support\Contracts\HasLabel;
use Modules\Support\Traits\CleanEnum;

enum TaskType: string implements HasLabel
{
    use CleanEnum;

    case MCQ = 'MCQ';
    case UploadFile = 'UploadFile';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MCQ => 'سوال چند گزینه ای',
            self::UploadFile => 'آپلود فایل',
        };
    }
}
