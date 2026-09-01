<?php

namespace App\Http\Resources;

use App\Models\Coin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Coin
 */
class CoinResource extends JsonResource
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
            'coin' => $this->coin,
            'created_at' => $this->created_at,
        ];
    }
}
