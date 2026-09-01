<?php

namespace App\Filament\Resources\TaskReviewResource\Pages;

use App\Filament\Resources\TaskReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskReviews extends ListRecords
{
    protected static string $resource = TaskReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
