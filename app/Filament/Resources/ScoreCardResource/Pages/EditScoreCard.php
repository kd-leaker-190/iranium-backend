<?php

namespace App\Filament\Resources\ScoreCardResource\Pages;

use App\Filament\Resources\ScoreCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScoreCard extends EditRecord
{
    protected static string $resource = ScoreCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
