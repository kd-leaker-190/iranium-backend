<?php

namespace App\Filament\Resources\MCQResource\Pages;

use App\Filament\Resources\MCQResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMCQ extends EditRecord
{
    protected static string $resource = MCQResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
