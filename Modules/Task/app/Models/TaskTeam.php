<?php

namespace Modules\Task\Models;

use App\Models\Team;
use Modules\FileUpload\Models\FileUpload;
use Modules\FileUpload\Models\FileUploadTeam;
use Modules\Task\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskTeam extends Model
{
    protected $table = 'task_team';

    protected $fillable = [
        'team_id',
        'task_id',
        'status',
        'done_at',
        'skipped_at',
    ]
    ;
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function getTaskTitleAttribute(): string
    {
        $taskable = $this->task?->taskable;

        if ($taskable instanceof FileUpload) {
            return $taskable->description;
        }

        return $this->task?->type?->value ?? '-';
    }
    public function fileUploadTeam(): ?FileUploadTeam
    {
        $fileUploadId = $this->task?->taskable_id;

        if (!$fileUploadId) {
            return null;
        }

        return FileUploadTeam::query()
            ->where('team_id', $this->team_id)
            ->where('file_upload_id', $fileUploadId)
            ->first();
    }

    private function cleanFilePart(string $value): string
    {
        $bad = ['/', '\\', ':', '*', '?', '"', '<', '>', '|'];
        $value = str_replace($bad, '-', trim($value));
        $value = preg_replace('/\s+/', ' ', $value);
        $value = preg_replace('/-+/', '-', $value);
        return trim($value, " -\t\n\r\0\x0B");
    }

    public function getDownloadFileName(Media $media): string
    {
        $teamName = $this->team?->name ?? 'team';
        $actionName = $this->task?->action?->name ?? 'action';
        $taskTitle = $this->task_title ?? 'task';

        $teamName = $this->cleanFilePart($teamName);
        $actionName = $this->cleanFilePart($actionName);
        $taskTitle = $this->cleanFilePart($taskTitle);

        $ext = $media->extension ?: pathinfo($media->file_name ?? '', PATHINFO_EXTENSION) ?: 'bin';

        return "{$teamName} - {$actionName} - {$taskTitle}.{$ext}";
    }
}
