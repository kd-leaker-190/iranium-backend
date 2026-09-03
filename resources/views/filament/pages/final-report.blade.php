<x-filament-panels::page>
    @livewire(\App\Filament\Widgets\FinalReportStatsWidget::class)

    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
