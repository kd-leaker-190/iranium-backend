<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActionResource;
use App\Models\Action;
use App\Models\ActionTeam;
use App\Models\Region;
use App\Models\ScoreTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\Support\Responses\ApiResponse;
use Modules\Task\Models\Task;
use Modules\FileUpload\Models\FileUploadTeam;
use Modules\FileUpload\Models\FileUpload;
use Modules\Task\Enum\TaskType;
use Modules\Task\Models\TaskTeam;

class ActionController extends Controller
{
    public function index()
    {
        $team = Auth::guard('team')->user();

        $data = Action::with(['region', 'actionTeams.team', 'actionTeams.team.tasks', 'icon'])->get();


        $data->loadCount('tasks');


        return ApiResponse::success([
            'actions' => ActionResource::collection($data),
            'meta' => [
                'actions' => [
                    'total' => Action::count(),
                    'completed' => ActionTeam::whereTeamId($team->id)
                        ->where('status', ActionStatus::Completed->value)
                        ->count(),
                ],
                'regions' => [
                    'total' => Region::count(),
                    'completed' => Region::whereHas('actions', function (Builder $actionQuery) use ($team) {
                        $actionQuery->whereHas('actionTeams', function (Builder $teamQuery) use ($team) {
                            $teamQuery->where('team_id', $team->id)
                                ->whereStatus(ActionStatus::Completed);
                        });
                    })->count(),
                ],
            ],
        ]);
    }

    public function start(Request $request, $action_id)
    {
        $team = Auth::guard('team')->user();

        $action = Action::findOrFail($action_id);

        if ($action->region->locked)
            return ApiResponse::fail('عملیات رزرو شده است', code: 'LOCKED');

        $actionTeam = ActionTeam::where('team_id', $team->id)->where('action_id', $action->id)->first();

        if (!$actionTeam) {
            $team->actions()->attach($action, [
                'status' => ActionStatus::Pending
            ]);

            if ($action->region->lockable) {
                $action->region->locked = true;
                $action->region->save();
            }

            return ApiResponse::success(new ActionResource($action), 'JOINED', 'عملیات با موفقیت شروع شد');
        }

        if ($actionTeam->status == ActionStatus::Pending) {
            return ApiResponse::fail('عملیات قبلا برای تیم شما شروع شده است.');
        }

        return ApiResponse::fail('عملیات به پایان رسیده را نمی‌توان شروع کرد.');
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function end(Request $request, Action $action)
    {
        $team = Auth::guard('team')->user();

        $actionTeam = ActionTeam::where('team_id', $team->id)->where('action_id', $action->id)->first();

        if (!$actionTeam) {
            return ApiResponse::fail('عملیات هنوز شروع نشده است.');
        }


        if ($actionTeam->status == ActionStatus::Completed) {
            return ApiResponse::fail('عملیات قبلا به پایان رسیده است.');
        }

        ScoreTeam::create([
            'team_id' => $team->id,
            'score' => $action->score,
            'scorable_id' => $action->id,
            'scorable_type' => Action::class,
        ]);

        if ($action->region->locked) {
            $action->region->locked = false;
            $action->region->save();
        }

        $team->actions()->updateExistingPivot($action->id, [
            'status' => ActionStatus::Completed
        ]);

        return ApiResponse::success(new ActionResource($action), 'JOINED', 'عملیات با موفقیت تکمیل شد');
    }

    public function show($action_id)
    {
        $team = Auth::guard('team')->user();

        $action = Action::with([
            'region',
            'tasks',
            'icon',
            'attachmentBoy',
            'attachmentGirl',
            'tasks.teams',
            'actionTeams'
        ])
            ->withCount('tasks')
            ->findOrFail($action_id);

        // وضعیت هر task برای این تیم: done / skipped
        $teamTaskStatuses = TaskTeam::query()
            ->where('team_id', $team->id)
            ->whereIn('task_id', $action->tasks->pluck('id'))
            ->pluck('status', 'task_id'); // [task_id => status]

        // آخرین order ای که تیم تعیین تکلیف کرده (done یا skipped) => برای unlock شدن تسک بعدی
        $teamLatestOrder = TaskTeam::query()
            ->where('task_team.team_id', $team->id)
            ->join('tasks', 'tasks.id', '=', 'task_team.task_id')
            ->where('tasks.action_id', $action_id)
            ->max('tasks.order');

        // برای UploadFileها، اطلاعات فایل‌های ارسالی تیم را یکجا بکشیم (برای جلوگیری از query زیاد)
        $uploadTaskableIds = $action->tasks
            ->where('type', TaskType::UploadFile)
            ->pluck('taskable_id')
            ->filter()
            ->unique()
            ->values();

        $fileUploadTeamsByUploadId = FileUploadTeam::query()
            ->where('team_id', $team->id)
            ->whereIn('file_upload_id', $uploadTaskableIds)
            ->get()
            ->keyBy('file_upload_id'); // [file_upload_id => FileUploadTeam]

        $action->tasks->map(function (Task $task) use ($teamTaskStatuses, $teamLatestOrder, $fileUploadTeamsByUploadId) {

            $task->can_edit = false;

            if ($task->type === TaskType::UploadFile) {
                $fileUploadTeam = $fileUploadTeamsByUploadId->get($task->taskable_id);

                // یعنی یک بار آپلود شده و هنوز ویرایش استفاده نشده
                $task->can_edit = $fileUploadTeam && !$fileUploadTeam->edit_used;
            }

            $status = $teamTaskStatuses[$task->id] ?? null; // done | skipped | null

            // locked: فقط یک تسک بعد از آخرین تعیین‌تکلیف باز باشد
            $task->locked_for_team = $task->order > (($teamLatestOrder ?? -1) + 1);

            // skipped flag برای UI
            $task->skipped_by_team = ($status === 'skipped');

            // done_by_team پیشفرض: فقط وقتی status=done
            $done = ($status === 'done');

            // UploadFile: done واقعی بعد از مصرف شدن ویرایش (edit_used=true)
            if ($task->type === TaskType::UploadFile) {
                if ($status === 'skipped') {
                    $done = false;
                } else {
                    $fileUploadTeam = $fileUploadTeamsByUploadId->get($task->taskable_id);
                    $done = $fileUploadTeam ? (bool)$fileUploadTeam->edit_used : false;
                }
            }

            $task->done_by_team = $done;

            return $task;
        });

        // فقط doneها را به عنوان completed بشمار (skip امتیاز ندارد)
        $teamCompletedTasks = TaskTeam::query()
            ->where('team_id', $team->id)
            ->where('status', 'done')
            ->whereIn('task_id', $action->tasks->pluck('id'))
            ->count();

        return ApiResponse::success([
            'team_completed_task_count' => $teamCompletedTasks,
            ...(new ActionResource($action))->toArray(request())
        ]);
    }
}
