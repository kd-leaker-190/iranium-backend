<?php


namespace Modules\Support\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function updated($data = [], string $message = 'اطلاعات بروزرسانی شد', mixed $code = 'SUCCESS'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ]);
    }

    public static function show($data = [], mixed $code = 'SUCCESS', string $message = ''): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ]);
    }

    public static function success($data = [], mixed $code = 'SUCCESS', string $message = ''): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ]);
    }

    public static function fail(string $message, $data = [], $errors = [], int $status = 400, mixed $code = 'UNEXPECTED'): JsonResponse
    {
        $translated = trans($message);
        if (!$translated || $translated == '')
            $translated = $message;
        return response()->json([
            'data' => $data,
            'errors' => $errors,
            'message' => $translated,
            'code' => $code,
        ], $status);
    }

    public static function created($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'code' => 'CREATED'
        ], 201);
    }

    public static function deleted(string $message = 'Resource Deleted'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => null,
            'code' => 'DELETED'
        ]);
    }
}
