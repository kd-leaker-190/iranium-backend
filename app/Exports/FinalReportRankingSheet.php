<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Morilog\Jalali\Jalalian;

class FinalReportRankingSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /** @param Collection<int, Team> $rankedTeams */
    public function __construct(protected Collection $rankedTeams, protected string $startDate)
    {
    }

    public function collection(): Collection
    {
        return $this->rankedTeams;
    }

    /** @param Team $team */
    public function map($team): array
    {
        return [
            $team->rank,
            $team->name,
            $team->team_identifier,
            // Cast to string: PhpSpreadsheet's Worksheet::fromArray() compares each value
            // against its null-marker with `==`, and PHP's `0 == null` is true, so a bare
            // int(0) is written as a blank cell instead of "0". A string "0" avoids that.
            (string) $team->score,
            (string) $team->coin,
            $team->gender ? 'پسر' : 'دختر',
            Jalalian::fromDateTime($team->start)->format('Y/m/d'),
        ];
    }

    public function headings(): array
    {
        return [
            'رتبه',
            'نام تیم',
            'شناسه تیم',
            'امتیاز',
            'سکه',
            'جنسیت',
            'کوهورت (تاریخ شروع)',
        ];
    }

    public function title(): string
    {
        return 'رده‌بندی';
    }
}
