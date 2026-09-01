<?php

namespace Modules\Support\Traits;

trait CleanEnum
{
    public static function lowercaseNames(): array
    {
        return array_map('strtolower', self::names());
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
