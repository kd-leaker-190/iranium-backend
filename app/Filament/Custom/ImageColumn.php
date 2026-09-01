<?php

namespace App\Filament\Custom;

use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ImageColumn
{
    public static function make(string $name): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make($name)
            ->translateLabel()
            ->collection($name);
    }
}
