<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'team_id',
        'ticket_category_id',
        'subject',
        'priority',
        'status',
        'closed_at'
    ];
    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'closed_at' => 'datetime'
        ];
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }
}
