<?php

namespace App\Exports;

use App\Models\Team;
use App\Services\FinalReportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinalReportExport implements WithMultipleSheets
{
    /** @param Collection<int, Team> $rankedTeams */
    public function __construct(protected Collection $rankedTeams)
    {
    }

    public function sheets(): array
    {
        $service = app(FinalReportService::class);

        return [
            new FinalReportRankingSheet($this->rankedTeams),
            new FinalReportScoreLedgerSheet($service->scoreLedger()),
            new FinalReportCoinLedgerSheet($service->coinLedger()),
        ];
    }
}
