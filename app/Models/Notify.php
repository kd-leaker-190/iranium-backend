<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notify extends Model
{
    protected $fillable = [
        'title',
        'content',
        'sms',
        'app',
        'release',
    ];
    protected $casts = [
        'release' => 'datetime',
        'sms' => 'boolean',
        'app' => 'boolean',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'notify_teams')
            ->using(NotifyTeam::class)
            ->withPivot(['is_read'])
            ->withTimestamps();
    }

    public function notifyTeams(): HasMany
    {
        return $this->hasMany(NotifyTeam::class);
    }
}
