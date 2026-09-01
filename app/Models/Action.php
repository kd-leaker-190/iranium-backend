<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Task\Models\Task;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Action extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'release',
        'region_id',
        'score'
    ];

    protected $casts = [
        'release' => 'datetime',
    ];

    protected $appends = [
        'estimated_time'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment_boy');
        $this->addMediaCollection('attachment_girl');
        $this->addMediaCollection('icon')->singleFile();
    }

    /**
     * @return MorphMany<Media,$this>
     */
    public function attachmentGirl(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'attachment_girl');
    }

    /**
     * @return MorphMany<Media,$this>
     */
    public function attachmentBoy(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'attachment_boy');
    }

    public function icon(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'icon');
    }

    public function dependency(): HasMany
    {
        return $this->hasMany(ActionDependency::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'action_team')->using(ActionTeam::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function actionTeams(): HasMany
    {
        return $this->hasMany(ActionTeam::class);
    }

    public function actionTeamFor(?int $teamId): ActionTeam|null
    {
        return $this->actionTeams->where('team_id', $teamId)->first();
    }

    protected function estimatedTime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tasks()->sum('duration'),
        );
    }
}
