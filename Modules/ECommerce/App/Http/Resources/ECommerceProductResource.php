<?php

namespace Modules\ECommerce\App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ECommerceProductResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
