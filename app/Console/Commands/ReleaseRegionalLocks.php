<?php

namespace App\Console\Commands;

use App\Enums\ActionStatus;
use App\Models\ActionTeam;
use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ReleaseRegionalLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release-regional-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release regional locks';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Region::where('locked', true)->chunkById(100, function ($regions) {
            $regions->each(function ($region) {

                $hasPending = ActionTeam::whereHas('action', function (Builder $query) use ($region) {
                    $query->where('region_id', $region->id);
                })->where('status', ActionStatus::Pending)->get();

                if ($hasPending->isEmpty()) {
                    $region->locked = false;
                    $region->save();
                    return;
                }
                $hasPending->each(function (ActionTeam $actionTeam) use ($region) {
                    if (now()->gt($actionTeam->created_at->addMinutes($actionTeam->action->estimated_time))) {
                        $actionTeam->status = ActionStatus::Timeout;
                        $actionTeam->save();

                        $region->locked = false;
                        $region->save();
                    }
                });
            });
        });
    }
}
