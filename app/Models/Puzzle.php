<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = ['name'];

    public function piece()
    {
        return $this->hasMany(PuzzlePiece::class);
    }
}
