<?php

namespace App\Http\Resources\V1\Address;

use App\Models\Address;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Address
 */
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            "addressableType"   => $this->addressable_type,
            "addressableId"     => $this->addressable_id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            "country"           => $this->country,
            "state"             => $this->state,
            "city"              => $this->city,
            "zipCode"           => $this->zip_code,
            "address"           => $this->address,
            "primary"           => $this->primary === 1 ? true : false,
            "addressType"       => $this->address_type,
            "createdAt"         => $this->created_at,
            "updatedAt"         => $this->updated_at,
            "deletedAt"         => $this->deleted_at,
        ];
    }
}
