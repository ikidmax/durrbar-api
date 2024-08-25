<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Tag\Database\factories\TagFactory;
use Spatie\Tags\Tag as TagsTag;

class Tag extends TagsTag
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'tags';

    // protected static function newFactory(): TagFactory
    // {
    //     return TagFactory::new();
    // }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
