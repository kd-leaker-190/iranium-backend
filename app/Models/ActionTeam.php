<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ActionTeam extends Pivot implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'action_id',
        'team_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ActionStatus::class
        ];
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment')->singleFile();
    }

    /**
     * @return MorphOne<Media,$this>
     */
    public function attachment(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'attachment');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
