<?php

namespace Modules\FileUpload\Services;

use App\Models\ScoreTeam;
use App\Models\Team;
use DB;
use Modules\FileUpload\Models\FileUpload;
use Modules\FileUpload\Models\FileUploadTeam;
use Modules\Task\Enum\TaskType;
use Modules\Task\Exceptions\EditNotAllowedException;
use Modules\Task\Exceptions\TaskAlreadyDoneException;
use Modules\Task\Models\Task;
use Modules\Task\Models\TaskTeam;
use Throwable;

class FileUploadService
{
    /**
     * @throws TaskAlreadyDoneException
     * @throws Throwable
     */
    public function answer(Team $team, FileUpload $fileUpload, array $data): FileUploadTeam
    {
        $file = $data['file'];

        $task = Task::where('taskable_type', FileUpload::class)
            ->where('taskable_id', $fileUpload->id)
            ->where('type', TaskType::UploadFile->value)
            ->firstOrFail();

        // وضعیت task_team برای این تیم و تسک
        $taskTeam = TaskTeam::where('task_id', $task->id)
            ->where('team_id', $team->id)
            ->first();

        // اگر اسکیپ کرده باشد، دیگر اجازه انجام/ویرایش ندارد
        if ($taskTeam && $taskTeam->status === 'skipped') {
            // بهتره Exception جدا بدی، ولی فعلاً همین هم ok
            throw new TaskAlreadyDoneException();
        }

        // اگر رکورد task_team وجود دارد یعنی قبلاً Done شده و وارد حالت Edit می‌شویم
        if ($taskTeam) {
            $fileUploadTeam = FileUploadTeam::where('team_id', $team->id)
                ->where('file_upload_id', $fileUpload->id)
                ->firstOrFail();

            if ($fileUploadTeam->edit_used) {
                throw new TaskAlreadyDoneException();
            }

            return DB::transaction(function () use ($fileUploadTeam, $file) {
                $fileUploadTeam->addMedia($file)->toMediaCollection('file');

                $fileUploadTeam->update([
                    'edit_used' => true,
                    'edited_at' => now(),
                ]);

                return $fileUploadTeam;
            });
        }

        // بار اول آپلود: Done می‌شود و امتیاز می‌گیرد
        return DB::transaction(function () use ($team, $fileUpload, $file, $task) {
            TaskTeam::create([
                'task_id' => $task->id,
                'team_id' => $team->id,
                'status' => 'done',
                'done_at' => now(),
            ]);

            $fileUploadTeam = FileUploadTeam::create([
                'team_id' => $team->id,
                'file_upload_id' => $fileUpload->id,
                'edit_used' => false,
                'submitted_at' => now(),
            ]);

            ScoreTeam::create([
                'team_id' => $team->id,
                'score' => $task->score,
                'scorable_id' => $task->id,
                'scorable_type' => Task::class,
            ]);

            $fileUploadTeam->addMedia($file)->toMediaCollection('file');

            return $fileUploadTeam;
        });
    }
}
