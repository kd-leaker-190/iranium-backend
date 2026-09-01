<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use Modules\Support\Responses\ApiResponse;

class TeamController extends Controller
{
    public function update(UpdateTeamRequest $request)
    {
        $data = $request->validated();
        $team = $request->user('team');
        $team->update($data);
        return ApiResponse::success(new TeamResource($team));
    }
}
