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
            'address' => 'App\Models\Address',
            'color' => 'Modules\Color\App\Models\Color',
            'comment' => 'App\Models\Comment',
            'image' => 'App\Models\Image',
            'invoice' => 'App\Models\Invoice\Invoice',
            'order' => 'App\Models\Order',
            'post' => 'App\Models\Post',
            'product' => 'App\Models\ECommerce\ECommerceProduct',
            'review' => 'App\Models\Review',
            'size' => 'Modules\Size\App\Models\Size',
            'tag' => 'App\Models\Tag',
            'user' => 'App\Models\User\User',
            'wishlist' => 'App\Models\Wishlist',
        ]);
    }
}
