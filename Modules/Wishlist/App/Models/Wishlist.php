<?php

namespace Modules\Wishlist\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\App\Models\Product;
use Modules\User\App\Models\User;

use Modules\Wishlist\Database\factories\WishlistFactory;

class Wishlist extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'wishlists';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): WishlistFactory
    {
        return WishlistFactory::new();
    }

    /**
     * Get the parent wishlistable model (post or video).
     */
    public function wishlistable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the wishlists relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function products(): MorphMany
    {
        return $this->morphMany(Product::class, 'productable');
    }

    /**
     * Return the user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
