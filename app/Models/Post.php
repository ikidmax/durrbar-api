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
use Illuminate\Support\Facades\Auth;
use Leshkens\LaravelReadTime\Traits\HasReadTime;

class Post extends Model implements Searchable
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use HasTags;
    use HasSlug;
    use HasReadTime;

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

    protected $casts = [
        'meta_keywords' => 'array', // Automatically casts to/from JSON
    ];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    protected function readTime(): array
    {
        return [
            // Attribute for parse. You can split it with 
            // a dot (e.g 'content.text') if the desired 
            // attribute is inside a array or json
            'source' => 'content',

            // No required. If this key is not present, then the current application locale is taken.
            'locale' => 'en',

            // No required. Options array.
            'options' => [
                'strip_tags' => false
            ]
        ];
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
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
