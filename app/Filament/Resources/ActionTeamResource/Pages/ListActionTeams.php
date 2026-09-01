<?php

namespace App\Filament\Resources\ActionTeamResource\Pages;

use App\Filament\Resources\ActionTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActionTeams extends ListRecords
{
    protected static string $resource = ActionTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
