<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArGameTeam extends Model
{
    protected $fillable = [
        'team_id',
        'ar_game_id'
    ];
}
