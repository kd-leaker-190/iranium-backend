<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\ScoreCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ScoreTeam;
use Modules\Support\Responses\ApiResponse;

class ScoreController extends Controller
{
    public function index()
    {
        $team = Auth::guard('team')->user();

        return ApiResponse::success([
            'total_score' => ScoreTeam::where('team_id', $team->id)->sum('score'),
            'incoming_score' => ScoreTeam::where('team_id', $team->id)->whereIn('scorable_type', [Game::class, ScoreCard::class])->sum('score'),
        ]);
    }
}
