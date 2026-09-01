<?php

namespace App\Filament\Custom;

use Filament\Forms;
use Spatie\MediaLibrary\HasMedia;

class FileInput
{
    public static function make(Forms\Form $form, string $name, bool $isMultiple = false, string $visibility = 'private'): Forms\Components\SpatieMediaLibraryFileUpload
    {
        return Forms\Components\SpatieMediaLibraryFileUpload::make($name)
            ->collection($name)
            ->translateLabel()
            ->deleteUploadedFileUsing(function (string $file) use ($name, $form) {
                if (! $file) {
                    return;
                }

                $record = $form->getRecord();

                if (! $record instanceof HasMedia) {
                    return;
                }

                $record
                    ->media()
                    ->where('collection_name', $name)
                    ->where('uuid', $file)
                    ->first()
                    ?->delete();
            })
            // ->disk(config('app.env') === 'production' ? 's3' : 'public')
            ->disk('ftp')
            ->visibility($visibility)
            ->multiple($isMultiple)
            ->reorderable($isMultiple)
            ->openable()
            ->downloadable();
    }
}
