<?php

namespace App\Http\Resources\V1\User;

use App\Models\User\User;
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
            'avatarUrl'   => $this->photo_url,
            'firstName'   => $this->first_name,
            'lastName'    => $this->last_name,
            'phone'       => $this->phone,
            'birthday'    => $this->birthday,
            'gender'      => $this->gender,
            'has2FA'      => $this->two_factor_confirmed_at ? true : false,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
        ];
    }
}
