<?php

use App\Http\Controllers\V1\ECommerce\ECommerceProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Post\PostController;
use App\Http\Resources\V1\Tag\TagResource;
use App\Models\Tag;

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

Route::middleware(['auth:sanctum'])->name('dashboard.')->prefix('dashboard')->group(function () {
    Route::apiResource('posts', PostController::class)->withTrashed()->names('posts');
    Route::apiResource('products', ECommerceProductController::class)->withTrashed()->names('products');
    Route::get('tag', function () {
    return ['tags' => TagResource::collection(Tag::all())];
});
});

Route::controller(PostController::class)->name('posts.')->prefix('posts')->group(function () {

    Route::get('featureds', 'featured')->name('featured');

    Route::get('latest', 'latest')->name('latest');

    Route::get('search', 'search')->name('search');
});

Route::apiResource('posts', PostController::class)->only(['index', 'show'])->scoped(['post' => 'slug']);

//



Route::controller(ECommerceProductController::class)->name('products.')->prefix('products')->group(function () {

    Route::get('featureds', 'featured')->name('featured');

    Route::get('latest', 'latest')->name('latest');

    Route::get('search', 'search')->name('search');
});

Route::apiResource('products', ECommerceProductController::class);
