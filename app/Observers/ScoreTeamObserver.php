<?php

namespace App\Observers;

use App\Models\ScoreTeam;
use App\Models\Team;

class ScoreTeamObserver
{
    public function saving(ScoreTeam $scoreTeam): void
    {
        Team::whereId($scoreTeam->team_id)->update([
            'score' => \DB::raw("score + $scoreTeam->score"),
        ]);
    }
}
