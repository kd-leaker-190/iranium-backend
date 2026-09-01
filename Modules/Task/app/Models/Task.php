<?php

namespace Modules\Task\Models;

use App\Models\Action;
use App\Models\ScoreTeam;
use App\Models\Team;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Task\Database\Factories\TaskFactory;
use Modules\Task\Enum\TaskType;
use Modules\Task\Observers\TaskObserver;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use HasFactory;

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    protected $fillable = [
        'taskable_type',
        'taskable_id',
        'type',
        'duration',
        'score',
        'order',
        'need_review',
        'action_id'
    ];

    protected function casts(): array
    {
        return [
            'type' => TaskType::class,
            'need_review' => 'boolean',
        ];
    }

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function score(): MorphMany
    {
        return $this->morphMany(ScoreTeam::class, 'scorable');
    }

    protected function doneByTeam(): Attribute
    {
        return Attribute::make(
            set: fn($value) => (bool)$value,
        );
    }

    protected function lockedForTeam(): Attribute
    {
        return Attribute::make(
            set: fn($value) => (bool)$value,
        );
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function taskTeam(): HasMany
    {
        return $this->hasMany(TaskTeam::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }
}
