<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraScore extends Model
{
    protected $fillable = [
        'team_id',
        'reason',
        'score',

    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
