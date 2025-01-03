<?php

namespace App\Models\ECommerce;

use App\Models\Image;
use App\Models\Tag;
use App\Models\Traits\ReviewableRateable;
use App\Models\Variant;
use App\Traits\HasVariants;
use Database\Factories\ECommerce\ECommerceProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class ECommerceProduct extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use HasTags;
    use HasVariants;
    use ReviewableRateable;

    protected $table = 'ecommerce_products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'sub_description',
        'price',
        'category',
        'publish',
        'available',
        'price_sale',
        'taxes',
        'quantity',
        'inventory_type',
        'new_label_enabled',
        'new_label_content',
        'sale_label_enabled',
        'sale_label_content',
        'total_sold'
    ];

    protected $casts = [
        'gender' => 'array',
        'new_label' => 'array',
        'sale_label' => 'array',
        'colors' => 'array',
        'price' => 'float',
        'price_sale' => 'float',
        'taxes' => 'float',
    ];

    protected static function newFactory(): ECommerceProductFactory
    {
        return ECommerceProductFactory::new();
    }

    /**
     * Get all images for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the cover image for the product.
     *
     * @return \App\Models\Image|null
     */
    public function cover(): ?Image
    {
        return $this->images()->first();
    }

    public function variants(): MorphToMany
    {
        return $this->morphToMany(Variant::class, 'variantable');
    }

    /**
     * Return the tags relationship.
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany($this->getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
