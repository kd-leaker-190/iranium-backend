<?php

namespace App\Filament\Resources\ScoreTeamResource\Pages;

use App\Filament\Resources\ScoreTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScoreTeams extends ListRecords
{
    protected static string $resource = ScoreTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
