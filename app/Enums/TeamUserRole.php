<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TeamUserRole: string implements HasLabel
{
    case SUCCESSOR = 'successor'; // جانشین
    case HUMAN_RESOURCE = 'human_resource'; // نیروی انسانی
    case ACADEMIC = 'academic'; // علمی تحصیلی
    case CONSTRUCTIVENESS_AND_EFFICIENCY = 'constructiveness_and_efficiency'; // سازندگی و کارآمدی
    case CULTURAL = 'cultural'; // فرهنگی
    case EDUCATION_AND_TRAINING_OF_FAMOUS_ASSISTANTS = 'education_and_training_of_famous_assistants'; // تعلیم و تربیت یاوران معروف
    case JOURNALISM = 'journalism'; // خبرنگاری
    case SUPPORT = 'support'; // پشتیبانی
    case INSPECTION = 'inspection'; // بازرسی
    case PHYSICAL_EDUCATION = 'physical_education'; // تربیت بدنی

    public function getLabel(): string
    {
        return match ($this) {
            self::SUCCESSOR => 'جانشین',
            self::HUMAN_RESOURCE => 'نیروی انسانی',
            self::ACADEMIC => 'علمی تحصیلی',
            self::CONSTRUCTIVENESS_AND_EFFICIENCY => 'سازندگی و کارآمدی',
            self::CULTURAL => 'فرهنگی',
            self::EDUCATION_AND_TRAINING_OF_FAMOUS_ASSISTANTS => 'تعلیم و تربیت یاوران معروف',
            self::JOURNALISM => 'خبر نگاری',
            self::SUPPORT => 'پشتیبانی',
            self::INSPECTION => 'بازرسی',
            self::PHYSICAL_EDUCATION => 'تربیت بدنی'
        };
    }
}
