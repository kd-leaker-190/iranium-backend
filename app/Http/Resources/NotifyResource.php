<?php

namespace App\Http\Resources;

use App\Models\Notify;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Notify
 */
class NotifyResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'release' => $this->release,
            'is_read' => (bool) ($this->pivot?->is_read ?? false),
            'created_at' => $this->created_at,
        ];
    }
}
