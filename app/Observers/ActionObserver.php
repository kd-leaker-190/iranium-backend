<?php

namespace App\Observers;

use App\Models\Action;
use Modules\Task\Models\Task;

class ActionObserver
{
    private function handleTasksOrder(Action $action): void
    {
        $action->tasks()->orderBy('order')->get()->each(function (Task $task, $i) {
            $task->order = $i;
            $task->save();
        });
    }
    /**
     * Handle the Action "created" event.
     */
    public function created(Action $action): void
    {
        $this->handleTasksOrder($action);
    }

    /**
     * Handle the Action "updated" event.
     */
    public function updated(Action $action): void
    {
        $this->handleTasksOrder($action);
    }

    /**
     * Handle the Action "deleted" event.
     */
    public function deleted(Action $action): void
    {
        //
    }

    /**
     * Handle the Action "restored" event.
     */
    public function restored(Action $action): void
    {
        //
    }

    /**
     * Handle the Action "force deleted" event.
     */
    public function forceDeleted(Action $action): void
    {
        //
    }
}
