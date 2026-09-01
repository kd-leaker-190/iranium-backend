<?php

namespace App\Filament\Widgets;

use App\Services\FinalReportService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinalReportStatsWidget extends StatsOverviewWidget
{
    public ?string $startDate = null;

    protected function getStats(): array
    {
        if (!$this->startDate) {
            return [];
        }

        $service = app(FinalReportService::class);
        $ranked = $service->rankedTeams($this->startDate);
        $summary = $service->summary($ranked);

        [$first, $second, $third] = [
            $summary['top3']->get(0),
            $summary['top3']->get(1),
            $summary['top3']->get(2),
        ];

        return [
            Stat::make('تعداد تیم‌ها', number_format($summary['total_teams'])),

            Stat::make('مجموع امتیاز', number_format($summary['total_score']))
                ->color('info'),

            Stat::make('مجموع سکه', number_format($summary['total_coin']))
                ->color('warning'),

            Stat::make('تیم برنده (رتبه ۱)', $first?->name ?? '—')
                ->description($first ? "امتیاز: {$first->score} | سکه: {$first->coin}" : null)
                ->color('success'),

            Stat::make('رتبه ۲', $second?->name ?? '—')
                ->description($second ? "امتیاز: {$second->score} | سکه: {$second->coin}" : null),

            Stat::make('رتبه ۳', $third?->name ?? '—')
                ->description($third ? "امتیاز: {$third->score} | سکه: {$third->coin}" : null),
        ];
    }
}
