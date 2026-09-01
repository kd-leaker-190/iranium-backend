<?php

namespace Modules\FileUpload\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Task\Models\Task;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FileUpload extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['description'];

    public function task(): MorphOne
    {
        return $this->morphOne(Task::class, 'taskable');
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'file_upload_team');
    }
}
