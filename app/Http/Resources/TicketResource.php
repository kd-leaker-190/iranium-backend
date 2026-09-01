<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class TicketResource extends JsonResource
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
            'subject' => $this->subject,
            'category' => new TicketCategoryResource($this->whenLoaded('ticketCategory')),
            'priority' => $this->priority?->value,
            'status' => $this->status?->value,
            'closed_at' => $this->closed_at ? Jalalian::fromDateTime($this->closed_at)->format('%A, %d %B %Y') : null,
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
            'created_at' => $this->created_at ? Jalalian::fromDateTime($this->created_at)->format('%A, %d %B %Y') : null,
        ];
    }
}
