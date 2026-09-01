<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">انتخاب کوهورت / رویداد</x-slot>

        {{ $this->form }}
    </x-filament::section>

    @if ($startDate)
        @livewire(\App\Filament\Widgets\FinalReportStatsWidget::class, ['startDate' => $startDate], key($startDate))
    @endif

    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
