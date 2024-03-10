<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class CustomPolymorphicTypesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'address' => 'Modules\Address\App\Models\Address',
            'color' => 'Modules\Color\App\Models\Color',
            'comment' => 'Modules\Comment\App\Models\Comment',
            'image' => 'Modules\Image\App\Models\Image',
            'invoice' => 'Modules\Invoice\App\Models\Invoice',
            'order' => 'Modules\Order\App\Models\Order',
            'post' => 'Modules\Post\App\Models\Post',
            'product' => 'Modules\Product\App\Models\Product',
            'review' => 'Modules\Review\App\Models\Review',
            'size' => 'Modules\Size\App\Models\Size',
            'tag' => 'Modules\Tag\App\Models\Tag',
            'user' => 'Modules\User\App\Models\User',
            'wishlist' => 'Modules\Wishlist\App\Models\Wishlist',
        ]);
    }
}
