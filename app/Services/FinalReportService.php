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
 * Single source of truth for the ranking population/order used by the Final Report
 * (Filament page, PDF, Excel export) AND the public Leaderboard API.
 * Reuses teams.score / teams.coin (kept up to date by ScoreTeamObserver / TeamCoinObserver)
 * as the ranking source, and the ScoreTeam / TeamCoin ledgers for the per-team breakdown.
 *
 * All teams are eligible for ranking — `start` is a registration timestamp only and must
 * never be used to filter which teams participate (see PR discussion: a prior cohort-by-date
 * heuristic here excluded ~1/3 of production teams, many with real, active scores).
 */
class FinalReportService
{
    /**
     * Base ranking query, all teams: score DESC, coin DESC, id ASC (deterministic tiebreak).
     * Exposed so the Filament table and the Leaderboard API can share the exact same
     * population/order instead of duplicating the ranking rule elsewhere.
     */
    public function rankedTeamsQuery(): Builder
    {
        return Team::query()
            ->orderByDesc('score')
            ->orderByDesc('coin')
            ->orderBy('id');
    }

    /**
     * All teams, ranked score DESC, coin DESC, id ASC, with sequential rank numbers.
     *
     * @return Collection<int, Team>
     */
    public function rankedTeams(): Collection
    {
        return $this->rankedTeamsQuery()
            ->get()
            ->values()
            ->each(function (Team $team, int $index) {
                $team->rank = $index + 1;
            });
    }

    /**
     * Executive summary block for the ranked teams.
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
     * Every ScoreTeam ledger row, for all teams, labelled the same way as teamBreakdown().
     * Used by the Excel export's detail sheet so the labeling logic isn't duplicated.
     *
     * @return Collection<int, array{team: string, team_identifier: string, source: string, amount: int, created_at: ?\Illuminate\Support\Carbon}>
     */
    public function scoreLedger(): Collection
    {
        return ScoreTeam::with(['scorable', 'team'])
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
     * Every TeamCoin ledger row, for all teams, labelled the same way as teamBreakdown().
     *
     * @return Collection<int, array{team: string, team_identifier: string, source: string, amount: int, created_at: ?\Illuminate\Support\Carbon}>
     */
    public function coinLedger(): Collection
    {
        return TeamCoin::with(['coinType', 'team'])
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
        $coinName = $entry->coinType?->name;
        $comment = $entry->comment;

        return trim(implode(' — ', array_filter([$coinName, $comment])) ?: 'دریافت سکه');
    }
}
