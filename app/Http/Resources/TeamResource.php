<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class TeamResource extends JsonResource
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
            'name' => $this->name,
            'color' => $this->color,
            'content' => $this->content,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'score' => $this->score,
            'coin' => $this->coin,
            'hash' => $this->hash,
            'gender' => $this->gender,
            'start' => $this->start,
            'm_c_qs_count' => $this->m_c_qs_count,
            'actions_count' => $this->actions_count,
            'tasks_count' => $this->tasks_count,
        ];
    }
}
