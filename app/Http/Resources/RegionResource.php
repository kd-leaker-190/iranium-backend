<?php

namespace App\Http\Resources;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Region
 */
class RegionResource extends JsonResource
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
            'x' => $this->x,
            'y' => $this->y,
            'order' => $this->order,
            'lockable' => $this->lockable,
            'locked' => $this->locked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
