<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArGame;
use App\Models\ArGameTeam;
use App\Models\ScoreTeam;
use App\Models\Team;
use Illuminate\Http\Request;
use Modules\Support\Responses\ApiResponse;

class ArGameController extends Controller
{
    public function exchange(Request $request, ArGame $arGame)
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $team = Team::where('phone', $request->phone)->first();

        if (!$team) {
            info('team not found', ['phone' => $request->phone]);
            return ApiResponse::fail('تیم یافت نشد');
        }

        if (ArGameTeam::where('team_id', $team->id)->count() > 15) {
            return ApiResponse::fail('شما قبلا 15 بار این بازی را انجام داده اید');
        }

        $arGameTeam = ArGameTeam::createOrUpdate([
            'team_id' => $team->id,
            'ar_game_id' => $arGame->id,
        ]);

        if ($arGameTeam->wasRecentlyCreated) {
            ScoreTeam::create([
                'team_id' => $team->id,
                'score' => 10,
                'scorable_id' => $arGame->id,
                'scorable_type' => ArGame::class,
            ]);
        }

        return ApiResponse::success('با موفقیت امتیاز شما اضافه شد');
    }
}
