<?php

namespace App\Filament\Resources\TeamCoinResource\Pages;

use App\Filament\Resources\TeamCoinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamCoin extends EditRecord
{
    protected static string $resource = TeamCoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
