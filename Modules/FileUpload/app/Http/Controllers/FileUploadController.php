<?php

namespace Modules\FileUpload\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\FileUpload\Http\Requests\AnswerFileUploadRequest;
use Modules\FileUpload\Models\FileUpload;
use Modules\FileUpload\Services\FileUploadService;
use Modules\Support\Responses\ApiResponse;
use Modules\Task\Exceptions\TaskAlreadyDoneException;
use Throwable;

class FileUploadController extends Controller
{
    public function __construct(private readonly FileUploadService $fileUploadService)
    {
        //
    }

    public function answer(AnswerFileUploadRequest $request, $fileUploadId)
    {
        $team = $request->user('team');
        $data = $request->validated();

        $fileUpload = FileUpload::findOrFail($fileUploadId);

        // try {
        //     try {
        //         $fileUploadTeam = $this->fileUploadService->answer($team, $fileUpload, $data);
        //         return ApiResponse::success($fileUploadTeam);
        //     } catch (TaskAlreadyDoneException|Throwable $e) {
        //         return ApiResponse::fail('خطا ناشناخته ای رخ داد.');
        //     }
        // } catch (TaskAlreadyDoneException $e) {
        //     return ApiResponse::fail('قبلا این وظیفه را انجام داده اید.');
        // }

        try {
            $fileUploadTeam = $this->fileUploadService->answer($team, $fileUpload, $data);
            return ApiResponse::success($fileUploadTeam);
        } catch (TaskAlreadyDoneException $e) {
            return ApiResponse::fail('قبلا این وظیفه را انجام داده اید.');
        } catch (Throwable $e) {
            \Log::error('File upload failed', ['e' => $e]);
            return ApiResponse::fail('خطا ناشناخته ای رخ داد.');
        }

    }
}
