<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NotifyTeam extends Pivot
{
    protected $table = 'notify_teams';

    protected $fillable =  [
        'notify_id',
        'team_id',
        'is_read'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function notify(): BelongsTo
    {
        return $this->belongsTo(Notify::class, 'notify_id');
    }
}
