<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coin extends Model
{
    protected $fillable = [
        'name',
        'coin'
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'coin_team');
    }
}
