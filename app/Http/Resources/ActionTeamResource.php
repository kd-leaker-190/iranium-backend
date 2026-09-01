<?php

namespace App\Http\Resources;

use App\Models\ActionTeam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ActionTeam
 */
class ActionTeamResource extends JsonResource
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
            'action_id' => $this->action_id,
            'team_id' => $this->team_id,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
        ];
            
    }
}
