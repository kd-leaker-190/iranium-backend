<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'title',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'game_teams');
    }
}
