<?php

namespace App\Filament\Resources\NotifyResource\Pages;

use App\Filament\Resources\NotifyResource;
use App\Jobs\SendNotifyJob;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateNotify extends CreateRecord
{
    protected static string $resource = NotifyResource::class;

    protected function afterCreate(): void
    {
        // if (isset($this->record?->sms)) {
        //    Log::error('dispatching notify job');
        //    SendNotifyJob::dispatch($this->record);
        // }

        if ($this->record->sms || $this->record->app) {
            SendNotifyJob::dispatch($this->record)->afterCommit()->delay($this->record->release);
        }
    }
}
