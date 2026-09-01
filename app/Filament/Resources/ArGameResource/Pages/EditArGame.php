<?php

namespace App\Filament\Resources\ArGameResource\Pages;

use App\Filament\Resources\ArGameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArGame extends EditRecord
{
    protected static string $resource = ArGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
