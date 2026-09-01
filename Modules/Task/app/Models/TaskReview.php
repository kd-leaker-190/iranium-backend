<?php

namespace Modules\Task\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class TaskReview extends Model
{
    protected $fillable = ['task_id', 'team_id', 'attachment', 'score', 'comment'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
