<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ECommerce\App\Http\Controllers\ECommerceProductAdminController;
use Modules\ECommerce\App\Http\Controllers\ECommerceProductController;
use Modules\ECommerce\App\Models\ECommerceProduct;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('ecommerce', fn (Request $request) => $request->user())->name('ecommerce');
});


Route::prefix('v1')->name('api.')->group(function () {

    Route::middleware(['auth:sanctum'])->prefix('/dashboard/e-commerce')->group(function () {

        Route::apiResource('products', ECommerceProductAdminController::class)->withTrashed()->names('admin.post');

        Route::post('products/image', [ECommerceProductAdminController::class, 'storeImage']);
    });

    Route::prefix('e-commerce')->group(function () {

        Route::apiResource('products', ECommerceProductController::class)->scoped(['post' => 'slug'])->only(['index', 'show']);

        Route::apiResource('products.reviews', ProductsCommentsController::class)->only(['index'])->scoped(['post' => 'slug']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::apiResource('products.reviews', ProductsCommentsController::class)->only(['store'])->scoped(['post' => 'slug']);

            Route::apiResource('products.reviews.replies', ProductsCommentsRepliesController::class)->only(['store'])->scoped(['post' => 'slug']);
        });

        Route::controller(ECommerceProductController::class)->prefix('product')->name('products.')->group(function () {

            Route::get('featureds', 'featured')->name('featured');

            Route::get('latest', 'latest')->name('latest');

            Route::get('search', 'search')->name('search');
        });
    });
});
