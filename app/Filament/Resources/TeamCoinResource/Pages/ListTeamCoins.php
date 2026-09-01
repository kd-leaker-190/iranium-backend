<?php

namespace App\Filament\Resources\TeamCoinResource\Pages;

use App\Filament\Resources\TeamCoinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamCoins extends ListRecords
{
    protected static string $resource = TeamCoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
