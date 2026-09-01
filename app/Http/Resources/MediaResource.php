<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin Media
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $url = $this->disk == 's3' ?
        //     $this->getTemporaryUrl(now()->addMinutes(20))
        //     :route('api.media.download', $this->id);

        $baseUrl = rtrim(config('app.download_base_url'), '/');

        return [
            'id' => $this->id,
            // 'download_url' => $baseUrl . '/' . ltrim($this->getPathRelativeToRoot(), '/'),
            'download_url' => config('app.env') === 'local' ? route('api.media.download', $this->id) : $baseUrl . '/' . ltrim($this->getPathRelativeToRoot(), '/'),
            'uuid' => $this->uuid,
            'collection_name' => $this->collection_name,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'disk' => $this->disk,
            'conversions_disk' => $this->conversions_disk,
            'size' => $this->size,
            'type' => $this->type,
            'humanReadableSize' => $this->humanReadableSize,
        ];
    }
}
