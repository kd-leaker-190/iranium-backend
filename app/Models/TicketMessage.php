<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $table = 'ticket_messages';
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'body'
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function sender()
    {
        return $this->morphTo();
    }

    public function getSenderLabelAttribute()
    {
        return $this->sender instanceof User ? 'ادمین'
            : ($this->sender instanceof Team ? 'تیم' : 'نامشخص');
    }

    public function getSenderColorAttribute()
    {
        return $this->sender instanceof User ? 'primary'
            : ($this->sender instanceof Team ? 'info' : 'gray');
    }
}
