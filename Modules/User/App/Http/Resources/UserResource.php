<?php

namespace Modules\User\App\Http\Resources;

use Modules\User\App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'photo'       => $this->photo,
            'photo_url'   => $this->photo_url,
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'phone'       => $this->phone,
            'birthday'    => $this->birthday,
            'gender'      => $this->gender,
            'has2FA'      => $this->two_factor_secret ? true : false,
        ];
    }
}
