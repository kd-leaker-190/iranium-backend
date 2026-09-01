<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\Jalalian;

class TeamsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Team|Collection
    {
        return Team::select('name', 'coin', 'score', 'start')->get();
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->coin,
            $row->score,
            Jalalian::fromDateTime($row->start)->format("Y/m/d"),
        ];
    }

    public function headings(): array
    {
        return [
            'نام',
            'سکه',
            'امتیاز',
            'تاریخ شروع',
        ];
    }
}
