<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArGame extends Model
{
    /** @use HasFactory<\Database\Factories\ArGameFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
    ];
}
