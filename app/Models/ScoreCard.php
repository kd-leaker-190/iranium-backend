<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScoreCard extends Model
{
    protected $fillable  = [
        'name',
        'score'
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'score_card_team');
    }
}
