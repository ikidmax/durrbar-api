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
            'user' => 'Modules\User\App\Models\User',
            // 'video' => 'App\Models\Video',
        ]);
    }
}
