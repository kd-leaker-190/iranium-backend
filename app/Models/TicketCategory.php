<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $table = 'ticket_categories';

    protected $fillable = [
        'name'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
