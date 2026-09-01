<?php

namespace Modules\MCQ\Services;

use App\Models\ScoreTeam;
use App\Models\Team;
use DB;
use Modules\MCQ\Models\MCQ;
use Modules\MCQ\Models\MCQTeam;
use Modules\Task\Exceptions\TaskAlreadyDoneException;
use Modules\Task\Models\Task;
use Throwable;

class MCQService
{
    /**
     * @throws TaskAlreadyDoneException
     * @throws Throwable
     */
    public function answer(Team $team, MCQ $MCQ , array $data): MCQTeam
    {
        $answer = $data['answer'];
        $task = $MCQ->task;

        if ($team->tasks()->where('tasks.id', $task->id)->exists()) {
            throw new TaskAlreadyDoneException();
        }

        $mcqTeam = null;

        DB::transaction(function () use ($team, $task, &$mcqTeam, $answer, $MCQ) {
            $team->tasks()->attach($task->id);

            if ($answer == $MCQ->answer) {
                ScoreTeam::create([
                    'team_id' => $team->id,
                    'score' => $task->score,
                    'scorable_id' => $task->id,
                    'scorable_type' => Task::class,
                ]);
            }

            $mcqTeam = MCQTeam::create([
                'team_id' => $team->id,
                'm_c_q_id' => $MCQ->id,
                'answer' => $answer,
            ]);
        });

        return $mcqTeam;
    }
}
