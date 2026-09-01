<?php

namespace App\Filament\Resources\MCQResource\Pages;

use App\Filament\Resources\MCQResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMCQS extends ListRecords
{
    protected static string $resource = MCQResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
