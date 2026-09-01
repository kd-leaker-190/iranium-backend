<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Modules\Support\Responses\ApiResponse;

class LeaderboardController extends Controller
{
    public function index()
    {
        // all available dates
        $dates = Team::select('start')
            ->distinct()
            ->orderBy('start')
            ->pluck('start');

        // last available date
        $lastDate = Team::whereDate('start', '<', now())->max('start');

        if (!$lastDate) {
            return ApiResponse::success([
                'dates' => $dates,
                'last_date' => null,
                'teams' => [],
            ]);
        }

        // last available date teams
        $teams = Team::whereDate('start', $lastDate)
            ->orderBy('score', 'desc')
            ->orderBy('coin', 'desc')
            ->limit(10)
            ->get();

        return ApiResponse::success([
            'dates' => $dates,
            'last_date' => $lastDate,
            'teams' => TeamResource::collection($teams),
        ]);
    }
}
