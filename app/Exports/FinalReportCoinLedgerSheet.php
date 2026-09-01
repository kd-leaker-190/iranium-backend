<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Morilog\Jalali\Jalalian;

class FinalReportCoinLedgerSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected Collection $entries)
    {
    }

    public function collection(): Collection
    {
        return $this->entries;
    }

    public function map($entry): array
    {
        return [
            $entry['team'],
            $entry['team_identifier'],
            $entry['source'],
            // String cast: see FinalReportRankingSheet::map() for why (PhpSpreadsheet
            // otherwise writes a literal 0 amount as a blank cell).
            (string) $entry['amount'],
            $entry['created_at'] ? Jalalian::fromDateTime($entry['created_at'])->format('Y/m/d H:i') : '—',
        ];
    }

    public function headings(): array
    {
        return ['تیم', 'شناسه تیم', 'منبع سکه', 'مقدار', 'تاریخ'];
    }

    public function title(): string
    {
        return 'ریز سکه‌ها';
    }
}
