<?php

namespace App\Filament\Resources\ExtraScoreResource\Pages;

use App\Filament\Resources\ExtraScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtraScores extends ListRecords
{
    protected static string $resource = ExtraScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
