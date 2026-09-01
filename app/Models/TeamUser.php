<?php

namespace App\Models;

use App\Enums\TeamUserRole;
use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    protected $table = 'team_users';

    protected $fillable = [
        'team_id',
        'name',
        'last_name',
        'grade_level',
        'birth_date',
        'national_code',
        'role',
        'is_verified'
    ];

    protected function casts(): array
    {
        return [
            'role' => TeamUserRole::class,
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
