<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskCheckAnswerRequest;
use App\Models\Action;
use App\Models\ActionTeam;
use Illuminate\Support\Facades\Log;
use Modules\Support\Responses\ApiResponse;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ActionTeamController extends Controller
{
    public function upload(TaskCheckAnswerRequest $request, Action $action)
    {
        $action->load('actionTeams');

        $team = $request->user('team');

        $actionTeam = ActionTeam::where('action_id', $action->id)
            ->where('team_id', $team->id)
            ->where('action_team.status', ActionStatus::Completed->value)
            ->firstOrFail();

        try {
            $actionTeam->addMediaFromRequest("file")->toMediaCollection('attachment');
        } catch (FileDoesNotExist|FileIsTooBig $e) {
            Log::error($e->getMessage());
        }

        return ApiResponse::success([], 'success', 'Action team uploaded');
    }
}
