<?php

namespace App\Services;

use App\Models\Action;
use App\Models\ArGame;
use App\Models\Game;
use App\Models\ScoreCard;
use App\Models\ScoreTeam;
use App\Models\Team;
use App\Models\TeamCoin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\FileUpload\Models\FileUpload;
use Modules\MCQ\Models\MCQ;
use Modules\Task\Models\Task;

/**
 * Single source of truth for the Final Report feature (Filament page, PDF, Excel export).
 * Reuses teams.score / teams.coin (kept up to date by ScoreTeamObserver / TeamCoinObserver)
 * as the ranking source, and the ScoreTeam / TeamCoin ledgers for the per-team breakdown.
 */
class FinalReportService
{
    /**
     * Distinct cohorts (by start date), each with its team count, most populated first.
     *
     * @return Collection<int, object{start_date: string, team_count: int}>
     */
    public function cohorts(): Collection
    {
        return Team::selectRaw('DATE(start) as start_date, COUNT(*) as team_count')
            ->groupBy('start_date')
            ->orderByDesc('team_count')
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * The cohort (start date) containing the largest number of teams.
     * Deliberately NOT "most recent date" (LeaderboardController's rule), since stray/test
     * teams on later dates would otherwise be picked over the real event cohort.
     */
    public function defaultCohort(): ?string
    {
        return $this->cohorts()->first()?->start_date;
    }

    /**
     * Base ranking query for a cohort: score DESC, coin DESC, id ASC (deterministic tiebreak).
     * Exposed so the Filament table can paginate/sort server-side using the exact same rule
     * used for the winner/summary/PDF/Excel, instead of duplicating the ordering elsewhere.
     */
    public function rankedTeamsQuery(string $startDate): Builder
    {
        return Team::query()
            ->whereDate('start', $startDate)
            ->orderByDesc('score')
            ->orderByDesc('coin')
            ->orderBy('id');
    }

    /**
     * Teams for a cohort, ranked score DESC, coin DESC, id ASC, with sequential rank numbers.
     *
     * @return Collection<int, Team>
     */
    public function rankedTeams(string $startDate): Collection
    {
        return $this->rankedTeamsQuery($startDate)
            ->get()
            ->values()
            ->each(function (Team $team, int $index) {
                $team->rank = $index + 1;
            });
    }

    /**
     * Executive summary block for a ranked cohort.
     *
     * @param Collection<int, Team> $rankedTeams
     */
    public function summary(Collection $rankedTeams): array
    {
        return [
            'total_teams' => $rankedTeams->count(),
            'total_score' => (int) $rankedTeams->sum('score'),
            'total_coin' => (int) $rankedTeams->sum('coin'),
            'winner' => $rankedTeams->first(),
            'top3' => $rankedTeams->take(3),
        ];
    }

    /**
     * Per-team score/coin ledger breakdown, with a human-readable source label per entry.
     * Gracefully reports when a team's totals have no backing ledger rows (e.g. team #705)
     * instead of fabricating transactions.
     */
    public function teamBreakdown(Team $team): array
    {
        $scoreEntries = ScoreTeam::with('scorable')
            ->where('team_id', $team->id)
            ->latest()
            ->get()
            ->map(fn (ScoreTeam $entry) => [
                'label' => $this->describeScoreSource($entry),
                'amount' => $entry->score,
                'created_at' => $entry->created_at,
            ]);

        $coinEntries = TeamCoin::with('coin')
            ->where('team_id', $team->id)
            ->latest()
            ->get()
            ->map(fn (TeamCoin $entry) => [
                'label' => $this->describeCoinSource($entry),
                'amount' => $entry->coin,
                'created_at' => $entry->created_at,
            ]);

        $scoreLedgerSum = (int) $scoreEntries->sum('amount');
        $coinLedgerSum = (int) $coinEntries->sum('amount');

        return [
            'score_entries' => $scoreEntries,
            'coin_entries' => $coinEntries,
            'score_ledger_sum' => $scoreLedgerSum,
            'coin_ledger_sum' => $coinLedgerSum,
            // Flags the report/UI use to show "no ledger entries available" rather than
            // silently presenting a breakdown that doesn't add up to teams.score/coin.
            'score_reconciles' => $scoreEntries->isNotEmpty() && $scoreLedgerSum === (int) $team->score,
            'coin_reconciles' => $coinEntries->isNotEmpty() && $coinLedgerSum === (int) $team->coin,
        ];
    }

    /**
     * Every ScoreTeam ledger row for a cohort, labelled the same way as teamBreakdown().
     * Used by the Excel export's detail sheet so the labeling logic isn't duplicated.
     *
     * @return Collection<int, array{team: string, team_identifier: string, source: string, amount: int, created_at: ?\Illuminate\Support\Carbon}>
     */
    public function scoreLedgerForCohort(string $startDate): Collection
    {
        return ScoreTeam::with(['scorable', 'team'])
            ->whereHas('team', fn (Builder $q) => $q->whereDate('start', $startDate))
            ->orderBy('team_id')
            ->latest()
            ->get()
            ->map(fn (ScoreTeam $entry) => [
                'team' => $entry->team?->name ?? '—',
                'team_identifier' => $entry->team?->team_identifier ?? '—',
                'source' => $this->describeScoreSource($entry),
                'amount' => $entry->score,
                'created_at' => $entry->created_at,
            ]);
    }

    /**
     * Every TeamCoin ledger row for a cohort, labelled the same way as teamBreakdown().
     *
     * @return Collection<int, array{team: string, team_identifier: string, source: string, amount: int, created_at: ?\Illuminate\Support\Carbon}>
     */
    public function coinLedgerForCohort(string $startDate): Collection
    {
        return TeamCoin::with(['coin', 'team'])
            ->whereHas('team', fn (Builder $q) => $q->whereDate('start', $startDate))
            ->orderBy('team_id')
            ->latest()
            ->get()
            ->map(fn (TeamCoin $entry) => [
                'team' => $entry->team?->name ?? '—',
                'team_identifier' => $entry->team?->team_identifier ?? '—',
                'source' => $this->describeCoinSource($entry),
                'amount' => $entry->coin,
                'created_at' => $entry->created_at,
            ]);
    }

    protected function describeScoreSource(ScoreTeam $entry): string
    {
        $source = $entry->scorable;

        return match (true) {
            $source instanceof Action => 'تکمیل عملیات: ' . ($source->name ?? '—'),
            $source instanceof ArGame => 'بازی واقعیت افزوده: ' . ($source->title ?? '—'),
            $source instanceof Game => 'بازی: ' . ($source->title ?? '—'),
            $source instanceof ScoreCard => 'کارت امتیاز: ' . ($source->name ?? '—'),
            $source instanceof Task => $this->describeTaskSource($source),
            default => class_basename($entry->scorable_type) . ' #' . $entry->scorable_id,
        };
    }

    protected function describeTaskSource(?Task $task): string
    {
        if (!$task) {
            return 'وظیفه (حذف شده)';
        }

        $taskable = $task->taskable;

        return match (true) {
            $taskable instanceof MCQ => 'پاسخ سوال چند گزینه‌ای: ' . $taskable->question,
            $taskable instanceof FileUpload => 'آپلود فایل: ' . ($taskable->description ?? $task->action?->name ?? '—'),
            default => 'وظیفه عملیات: ' . ($task->action?->name ?? '—'),
        };
    }

    protected function describeCoinSource(TeamCoin $entry): string
    {
        $coinName = $entry->coin?->name;
        $comment = $entry->comment;

        return trim(implode(' — ', array_filter([$coinName, $comment])) ?: 'دریافت سکه');
    }
}
