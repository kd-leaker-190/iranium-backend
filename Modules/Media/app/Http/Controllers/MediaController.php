<?php

namespace Modules\Media\Http\Controllers;

use App\Http\Controllers\Controller;
// use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Task\Models\TaskTeam;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
// use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    // public function download(Request $request, Media $media)
    // {
    //     $fullPath = $media->getPath(); // مسیر کامل فایل

    //     abort_unless(is_file($fullPath), 404, 'File not found');

    //     return response()->download($fullPath, $media->file_name, [
    //         'Content-Type' => $media->mime_type,
    //     ]);
    // }

    // public function downloadTaskTeamMedia(Request $request, TaskTeam $taskTeam, Media $media): BinaryFileResponse
    // {
    // 1) بررسی اینکه این مدیا واقعاً متعلق به همین taskTeam هست (امنیت!)
    //     $fileUploadTeam = $taskTeam->fileUploadTeam();

    //     if (!$fileUploadTeam) {
    //         abort(404, 'File relation not found');
    //     }

    //     $expectedMediaId = $fileUploadTeam->getFirstMedia('file')?->id;

    //     abort_unless($expectedMediaId && (int) $expectedMediaId === (int) $media->id, 403, 'Unauthorized media');

    // 2) مسیر فایل
    //     $fullPath = $media->getPath();
    //     abort_unless(is_file($fullPath), 404, 'File not found');

    // 3) اسم دانلود سفارشی
    //     $downloadName = $taskTeam->getDownloadFileName($media);

    //     return response()->download($fullPath, $downloadName, [
    //         'Content-Type' => $media->mime_type,
    //     ]);
    // }

    public function download(Request $request, Media $media)
    {
        $disk = Storage::disk($media->disk);
        $relativePath = $media->getPathRelativeToRoot();

        abort_unless($disk->exists($relativePath), 404, 'File not found');

        $stream = $disk->readStream($relativePath);

        abort_unless($stream, 404, 'File not found');

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $media->file_name, [
            'Content-Type' => $media->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function downloadTaskTeamMedia(Request $request, TaskTeam $taskTeam, Media $media)
    {
        $fileUploadTeam = $taskTeam->fileUploadTeam();

        if (!$fileUploadTeam) {
            abort(404, 'File relation not found');
        }

        $expectedMediaId = $fileUploadTeam->getFirstMedia('file')?->id;

        abort_unless($expectedMediaId && (int) $expectedMediaId === (int) $media->id, 403, 'Unauthorized media');

        $disk = Storage::disk($media->disk);
        $relativePath = $media->getPathRelativeToRoot();

        abort_unless($disk->exists($relativePath), 404, 'File not found');

        $stream = $disk->readStream($relativePath);
        abort_unless($stream, 404, 'File not found');

        $downloadName = $taskTeam->getDownloadFileName($media);

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $downloadName, [
            'Content-Type' => $media->mime_type ?: 'application/octet-stream',
        ]);
    }
}
