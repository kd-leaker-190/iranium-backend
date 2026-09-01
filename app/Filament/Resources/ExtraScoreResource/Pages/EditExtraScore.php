<?php

namespace App\Filament\Resources\ExtraScoreResource\Pages;

use App\Filament\Resources\ExtraScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtraScore extends EditRecord
{
    protected static string $resource = ExtraScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
