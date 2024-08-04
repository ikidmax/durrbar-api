<?php

namespace App\Http\Resources\V1\Post;

use App\Traits\HasPagination;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    use HasPagination;
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => $this->pagination(),
        ];
    }
}
