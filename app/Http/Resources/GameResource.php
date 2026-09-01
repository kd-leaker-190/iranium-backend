<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'file_name' => $this->file_name,
            'title' => $this->title,
            // 'base_url' => 'https://s3.game.silitonix.ir/games/',
            'base_url' => 'https://backend.iraniumisf.ir/storage/games/',
            // 'base_url' => 'http://localhost:8000/storage/games/',
        ];
    }
}
