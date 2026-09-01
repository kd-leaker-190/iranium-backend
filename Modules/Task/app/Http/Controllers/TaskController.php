<?php

namespace Modules\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Support\Responses\ApiResponse;
use Modules\Task\Models\Task;
use Modules\Task\Models\TaskTeam;
use Modules\Task\Resources\TaskResource;

class TaskController extends Controller
{
    public function show(Task $task): JsonResponse
    {
        $task->load('taskable');
        $task->action->loadCount('tasks');

        return ApiResponse::success(new TaskResource($task));
    }

    public function skip(Request $request, Task $task)
    {
        $team = $request->user('team');

        $existing = TaskTeam::where('task_id', $task->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing) {
            return ApiResponse::fail('این وظیفه قبلا تعیین تکلیف شده است.');
        }

        TaskTeam::create([
            'task_id' => $task->id,
            'team_id' => $team->id,
            'status' => 'skipped',
            'skipped_at' => now(),
        ]);

        return ApiResponse::success(true, 'SKIPPED', 'وظیفه اسکیپ شد.');
    }
}
