<?php

namespace Modules\MCQ\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MCQTeam extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['answer', 'm_c_q_id', 'team_id'];

}
