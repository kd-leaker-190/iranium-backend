<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use Modules\Support\Responses\ApiResponse;
use App\Http\Resources\GameResource;
use App\Models\ScoreTeam;
use Illuminate\Support\Facades\Auth;
use App\Models\GameTeam;
use App\Models\ScoreCard;

class GameController extends Controller
{
    public function index()
    {
        $team = Auth::guard('team')->user();

        $data = Game::whereDoesntHave('teams', function ($query) use ($team) {
            $query->where('team_id', $team->id);
        })->get();

        return ApiResponse::success(GameResource::collection($data));
    }

    public function show(Game $game)
    {
        return ApiResponse::success(GameResource::make($game));
    }

    public function exchange(Request $request, Game $game)
    {
        $team = Auth::guard('team')->user();

        $request->validate([
            'score' => ['required', 'numeric', 'max:1000'],
        ]);

        if ($game->teams()->where('team_id', $team->id)->exists()) {
            return ApiResponse::fail('این بازی قبلا برای شما استفاده شده است.');
        }

        $score = $request->score > 100 ? 100 : $request->score;

        ScoreTeam::create([
            'team_id' => $team->id,
            'score' => $score,
            'scorable_id' => $game->id,
            'scorable_type' => Game::class,
        ]);

        $game->teams()->attach($team);

        return ApiResponse::success('با موفقیت امتیاز شما اضافه شد');
    }

    public function score()
    {
        $team = Auth::guard('team')->user();

        return ApiResponse::success([
            'total_score' => ScoreTeam::where('team_id', $team->id)->sum('score'),
            'incoming_score' => ScoreTeam::where('team_id', $team->id)->whereIn('scorable_type', [Game::class])->sum('score'),
        ]);
    }
}
