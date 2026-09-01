<?php

namespace App\Http\Resources;

use App\Models\Coin;
use App\Models\ScoreCard;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ScoreCard
 */
class ScoreCardResource extends JsonResource
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
            'title' => $this->name,
            'score' => $this->score,
            'created_at' => $this->created_at,
        ];
    }
}
