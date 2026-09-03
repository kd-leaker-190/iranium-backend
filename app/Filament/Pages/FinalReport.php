<?php

namespace App\Filament\Pages;

use App\Exports\FinalReportExport;
use App\Models\Team;
use App\Services\FinalReportService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FinalReport extends Page implements HasActions, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'گزارش‌ها';

    protected static ?string $navigationLabel = 'گزارش نهایی';

    protected static ?string $title = 'گزارش نهایی';

    protected static ?string $slug = 'final-report';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.final-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_FinalReport') ?? false;
    }

    public function table(Table $table): Table
    {
        $service = app(FinalReportService::class);

        return $table
            ->query(fn () => $service->rankedTeamsQuery())
            ->heading('جدول رده‌بندی')
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('rank')
                    ->label('رتبه')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->label('نام تیم')
                    ->searchable(),

                TextColumn::make('team_identifier')
                    ->label('شناسه تیم')
                    ->searchable(),

                TextColumn::make('score')
                    ->label('امتیاز')
                    ->numeric(),

                TextColumn::make('coin')
                    ->label('سکه')
                    ->numeric(),

                TextColumn::make('gender')
                    ->label('جنسیت')
                    ->formatStateUsing(fn (bool $state) => $state ? 'پسر' : 'دختر'),

                TextColumn::make('start')
                    ->label('تاریخ ثبت‌نام')
                    ->jalaliDateTime(),
            ])
            ->actions([
                TableAction::make('breakdown')
                    ->label('جزئیات امتیاز/سکه')
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray')
                    ->modalHeading(fn (Team $record) => 'ریز امتیاز و سکه — ' . $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('2xl')
                    ->modalContent(fn (Team $record) => view('filament.reports.team-breakdown', [
                        'team' => $record,
                        'breakdown' => app(FinalReportService::class)->teamBreakdown($record),
                    ])),
            ])
            ->defaultSort(null);
    }

    protected function getHeaderActions(): array
    {
        return [
            // No explicit ->color(): matches TeamResource's own "خروجی اکسل" export action,
            // which also relies on Filament's default action color rather than a custom one.
            Action::make('download_excel')
                ->label('خروجی اکسل')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->downloadExcel()),
        ];
    }

    public function downloadExcel()
    {
        $service = app(FinalReportService::class);
        $rankedTeams = $service->rankedTeams();

        $filename = 'گزارش-نهایی-' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new FinalReportExport($rankedTeams),
            $filename
        );
    }
}
