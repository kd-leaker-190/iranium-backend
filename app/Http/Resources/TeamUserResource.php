<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class TeamUserResource extends JsonResource
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
            'last_name' => $this->last_name,
            'grade_level' => $this->grade_level,
            'birth_date' => $this->birth_date,
            'national_code' => $this->national_code,
            'role' => $this->role?->getLabel(),
            'is_verified' => $this->is_verified,
            'team' => new TeamResource($this->whenLoaded('team')),
        ];
    }
}
