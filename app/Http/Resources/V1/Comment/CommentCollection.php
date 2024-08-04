<?php

namespace App\Http\Resources\V1\Comment;

use App\Traits\HasPagination;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
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
