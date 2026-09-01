<?php

namespace Modules\Support\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedCollection extends ResourceCollection
{
    private array $append;

    public function __construct($resource, string $collect, array $append = [])
    {
        $this->collects = $collect;
        $this->append = $append;
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->append)
            $data = [
                ...$this->append,
                'page_data' => $this->collection,
            ];
        else $data = $this->collection;
        return [
            'page_data' => $data,
            'total' => $this->resource->total(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'has_page' => $this->resource->hasPages(),
        ];
    }
}
