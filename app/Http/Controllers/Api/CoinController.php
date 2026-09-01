<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoinResource;
use App\Models\Coin;
use App\Models\CoinTeam;
use App\Models\ScoreTeam;
use App\Models\TeamCoin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\Support\Responses\ApiResponse;

class CoinController extends Controller
{
    public function index()
    {
        $data = Coin::get();
        return ApiResponse::success(CoinResource::collection($data));
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function exchange(Request $request, Coin $coin)
    {
        $team = Auth::guard('team')->user();

        if (TeamCoin::whereTeamId($team->id)->whereCoinId($coin->id)->exists()) {
            return ApiResponse::fail('کارت سکه قبلا برای تیم شما استفاده شده است.', code: 'ALREADY_SCANNED');
        }

        TeamCoin::create([
            'team_id' => $team->id,
            'coin_id' => $coin->id,
            'coin' => $coin->coin,
            'comment' => 'اسکن شده'
        ]);

        return ApiResponse::created(new CoinResource($coin), 'سکه ها با موفقیت اضافه شدند شد');
    }
}
