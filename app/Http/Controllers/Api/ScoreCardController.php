<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScoreCardResource;
use App\Models\ScoreCard;
use App\Models\ScoreTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\Support\Responses\ApiResponse;

class ScoreCardController extends Controller
{
    public function index()
    {
        $data = ScoreCard::get();
        return ApiResponse::success(ScoreCardResource::collection($data));
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function exchange(Request $request, ScoreCard $scoreCard)
    {
        $team = Auth::guard('team')->user();

        if (ScoreTeam::whereTeamId($team->id)
            ->whereScorableId($scoreCard->id)
            ->whereScorableType(ScoreCard::class)
            ->exists()) {
            return ApiResponse::fail('کارت امتیاز قبلا برای تیم شما استفاده شده است.', code: 'ALREADY_SCANNED');
        }

        ScoreTeam::create([
            'team_id' => $team->id,
            'scorable_id' => $scoreCard->id,
            'scorable_type' => ScoreCard::class,
            'score' => $scoreCard->score,
        ]);

        return ApiResponse::created(new ScoreCardResource($scoreCard), 'امتیاز ها با موفقیت اضافه شدند شد');
    }
}
