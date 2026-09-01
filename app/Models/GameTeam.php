<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameTeam extends Model
{
    protected $fillable = [
        'team_id',
        'game_id'
    ];
}
