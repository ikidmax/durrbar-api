<?php

namespace App\Http\Resources\V1\Comment;

use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "id"                => $this->id,
            "parentId"          => $this->whenHas('parent_id'),
            "userId"            => $this->whenHas('user_id'),
            "commentableType"   => $this->commentable_type,
            "commentableId"     => $this->commentable_id,
            "content"           => $this->content,
            "createdAt"         => $this->created_at,
            "updatedAt"         => $this->updated_at,
            "deletedAt"         => $this->deleted_at,
            "user"              => new UserResource($this->user),
            "replies"           => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
