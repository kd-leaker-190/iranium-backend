<x-filament-panels::page>
    <form wire:submit="sendSms">
        {{ $this->form }}
        
        <div class="mt-6">
            <x-filament::button type="submit" color="success" icon="heroicon-o-paper-airplane">
                ارسال پیامک
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page> 