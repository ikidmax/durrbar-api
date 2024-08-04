<?php

namespace App\Http\Resources\V1\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Comment\CommentCollection;
use App\Http\Resources\V1\User\UserResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'publish' => $this->publish,
            'featured' => $this->featured,
            'content' => $this->content,
            'authorId' => $this->author_id,
            'description' => $this->description,
            'duration' => $this->duration,
            'totalViews' => $this->total_views,
            'totalShares' => $this->total_share,
            'totalFavorites' => $this->total_favorites,
            'metaTitle' => $this->meta_title,
            'metaKeywords' => $this->meta_keywords,
            'metaDescription' => $this->meta_description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
            // 'comments' => $this->comments,
            'totalComments' => $this->whenCounted('comments'),
            'coverUrl' => $this->when($this->whenLoaded('cover'), $this->cover->url),
            'author' => new UserResource($this->whenLoaded('author')),
            'tags' => $this->tags
        ];
    }
}
// php artisan module:make-resource CommentCollection Comment --collection