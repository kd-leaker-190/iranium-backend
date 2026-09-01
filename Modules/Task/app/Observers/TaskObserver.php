<?php

namespace Modules\Task\Observers;


use Modules\FileUpload\Models\FileUpload;
use Modules\MCQ\Models\MCQ;
use Modules\Task\Enum\TaskType;
use Modules\Task\Models\Task;

class TaskObserver
{
    public function creating(Task $task): void
    {
        $type = match ($task->taskable_type) {
            MCQ::class => TaskType::MCQ,
            FileUpload::class => TaskType::UploadFile
        };

        $task->type = $type;

        if (Task::whereActionId($task->action_id)->exists()) {
            $task->order = Task::whereActionId($task->action_id)->max('order') + 1;
        }
        else
        {
            $task->order = 0;
        }
    }
}
