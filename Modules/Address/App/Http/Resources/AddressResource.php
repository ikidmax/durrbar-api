<?php

namespace Modules\Address\App\Http\Resources;

use Modules\Address\App\Models\Address;
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
            "addressable_type"  => $this->addressable_type,
            "addressable_id"    => $this->addressable_id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            "country"           => $this->country,
            "state"             => $this->state,
            "city"              => $this->city,
            "zip_code"          => $this->zip_code,
            "address"           => $this->address,
            "primary"           => $this->primary === 1 ? true : false,
            "address_type"      => $this->address_type,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at,
            "deleted_at"        => $this->deleted_at,
        ];
    }
}
