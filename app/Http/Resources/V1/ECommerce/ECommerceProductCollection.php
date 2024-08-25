<?php

namespace App\Http\Resources\V1\ECommerce;

use App\Traits\HasPagination;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ECommerceProductCollection extends ResourceCollection
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
