<?php

namespace App\Filament\Pages;

use App\Exports\FinalReportExport;
use App\Models\Team;
use App\Services\FinalReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FinalReport extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'گزارش‌ها';

    protected static ?string $navigationLabel = 'گزارش نهایی';

    protected static ?string $title = 'گزارش نهایی';

    protected static ?string $slug = 'final-report';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.final-report';

    public ?string $startDate = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_FinalReport') ?? false;
    }

    public function mount(): void
    {
        $this->startDate = app(FinalReportService::class)->defaultCohort();
        $this->form->fill(['startDate' => $this->startDate]);
    }

    public function getCohorts()
    {
        return app(FinalReportService::class)->cohorts();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('startDate')
                    ->label('کوهورت / رویداد')
                    ->helperText(
                        'گزارش بر اساس تاریخ شروع تیم‌ها (ستون start) گروه‌بندی می‌شود. به‌صورت پیش‌فرض ' .
                        'کوهورتی انتخاب شده که بیشترین تعداد تیم را دارد (نه لزوماً آخرین تاریخ) تا تیم‌های ' .
                        'تستی/پراکنده به‌اشتباه به‌عنوان رویداد اصلی نمایش داده نشوند.'
                    )
                    ->options(fn () => $this->getCohorts()->mapWithKeys(
                        fn ($cohort) => [
                            $cohort->start_date => \Morilog\Jalali\Jalalian::fromDateTime($cohort->start_date)->format('Y/m/d')
                                . ' (' . $cohort->start_date . ') — ' . number_format($cohort->team_count) . ' تیم',
                        ]
                    ))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ]);
    }

    public function table(Table $table): Table
    {
        $service = app(FinalReportService::class);

        return $table
            ->query(fn () => $this->startDate
                ? $service->rankedTeamsQuery($this->startDate)
                : Team::query()->whereRaw('1 = 0'))
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
                    ->label('تاریخ شروع (کوهورت)')
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
                ->disabled(fn () => !$this->startDate)
                ->action(fn () => $this->downloadExcel()),

            Action::make('download_pdf')
                ->label('دانلود PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->disabled(fn () => !$this->startDate)
                ->action(fn () => $this->downloadPdf()),
        ];
    }

    public function downloadExcel()
    {
        $service = app(FinalReportService::class);
        $rankedTeams = $service->rankedTeams($this->startDate);

        $filename = 'گزارش-نهایی-' . $this->startDate . '.xlsx';

        return Excel::download(
            new FinalReportExport($rankedTeams, $this->startDate),
            $filename
        );
    }

    public function downloadPdf()
    {
        $service = app(FinalReportService::class);
        $rankedTeams = $service->rankedTeams($this->startDate);
        $summary = $service->summary($rankedTeams);

        $pdf = Pdf::loadView('pdf.final-report', [
            'rankedTeams' => $rankedTeams,
            'summary' => $summary,
            'startDate' => $this->startDate,
            'generatedAt' => Carbon::now(),
            'appName' => config('app.name'),
        ])->setPaper('a4', 'portrait');

        // Render first so we can attach a page-number footer via the canvas directly,
        // without relying on dompdf's `enable_php` inline-script option (left disabled
        // project-wide in config/dompdf.php).
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('noto-sans-arabic', 'normal');
        $canvas->page_text(520, 812, 'صفحه {PAGE_NUM} از {PAGE_COUNT}', $font, 9, [0.35, 0.35, 0.35]);

        $filename = 'گزارش-نهایی-' . $this->startDate . '.pdf';

        return $pdf->download($filename);
    }
}
