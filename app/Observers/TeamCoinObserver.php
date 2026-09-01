<?php

namespace App\Observers;

use App\Models\Team;
use App\Models\TeamCoin;

class TeamCoinObserver
{
    public function saving(TeamCoin $teamCoin): void
    {
        Team::where('id', $teamCoin->team_id)
            ->update([
                'coin' => \DB::raw("coin + $teamCoin->coin"),
            ]);
    }
}
