<?php

namespace App\Filament\Resources\NotifyResource\Pages;

use App\Filament\Resources\NotifyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifies extends ListRecords
{
    protected static string $resource = NotifyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
