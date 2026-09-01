<?php

namespace App\Models;

use App\Observers\TeamCoinObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TeamCoinObserver::class)]
class TeamCoin extends Model
{
    protected $fillable = [
        'team_id',
        'coin',
        'comment',
        'coin_id'
    ];


    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
