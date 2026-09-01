<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): string
    {
        return asset('images/logo.png');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'age',
        'gender',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => 'boolean'
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // TODO
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function ticketMessages()
    {
        return $this->morphMany(TicketMessage::class, 'sender', 'sender_type', 'sender_id', 'id');
    }
}
