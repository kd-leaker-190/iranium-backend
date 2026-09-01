<?php

namespace App\Filament\Resources\ScoreCardResource\Pages;

use App\Filament\Resources\ScoreCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScoreCards extends ListRecords
{
    protected static string $resource = ScoreCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
