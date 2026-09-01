<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class TicketMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender_type === 'App\\Models\\User' ? 'admin' : 'team',
            'body' => $this->body,
            'created_at' => $this->created_at ? Jalalian::fromDateTime($this->created_at)->ago() : null,
        ];
    }
}
