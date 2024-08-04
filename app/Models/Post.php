<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Spatie\Sluggable\HasSlug;
use Spatie\Searchable\Searchable;
use Spatie\Sluggable\SlugOptions;
use Spatie\Searchable\SearchResult;
use Database\factories\PostFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

use App\Models\Tag;
use App\Models\Image;
use App\Models\Comment;
use App\Models\User\User;

class Post extends Model implements Searchable
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use HasTags;
    use HasSlug;

    /**
     * The table associated with the model.
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'publish',
        'content',
        'cover_url',
        'author_id',
        'meta_title',
        'total_views',
        'description',
        'total_shares',
        'meta_keywords',
        'total_favorites',
        'meta_description'
    ];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('api.posts.show', $this->slug);

        return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url
        );
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }

    /**
     * Get the author that owns the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the comments relationship.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Return the comments relationship.
     */
    public function cover(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Return the tags relationship.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
