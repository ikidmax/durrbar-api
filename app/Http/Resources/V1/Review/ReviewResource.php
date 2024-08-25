<?php

namespace App\Http\Resources\V1\Review;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Review
 */
class ReviewResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'postedAt'      => $this->created_at,
            'comment'       => $this->comment,
            'isPurchased'   => $this->is_purchased,
            'rating'        => $this->rating,
            'avatarUrl'     => $this->user->photo_url,
            'helpful'       => $this->helpful,
            'attachments'   => $this->attachments
        ];
    }
}
