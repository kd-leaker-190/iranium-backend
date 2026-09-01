<?php

namespace Modules\MCQ\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MCQ\Http\Requests\AnswerMCQRequest;
use Modules\MCQ\Models\MCQ;
use Modules\MCQ\Services\MCQService;
use Modules\Support\Responses\ApiResponse;
use Modules\Task\Exceptions\TaskAlreadyDoneException;
use Modules\Task\Models\Task;

class MCQController extends Controller
{
    public function __construct(
        private readonly MCQService $mcqService
    )
    {
    }

    public function answer(AnswerMCQRequest $request, MCQ $mcq)
    {
        $team = $request->user('team');
        $data = $request->validated();

        try {
            $mcqTeam = $this->mcqService->answer($team, $mcq, $data);
            $code = $mcqTeam->answer == $mcq->answer ? "CORRECT" : "INCORRECT";
            return ApiResponse::success($mcqTeam, $code);
        } catch (TaskAlreadyDoneException $e) {
            return ApiResponse::fail('قبلا این وظیفه را انجام داده اید.');
        }
    }
}
