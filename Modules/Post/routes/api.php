<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Post\App\Http\Controllers\PostAdminController;
use Modules\Post\App\Http\Controllers\PostController;
use Modules\Post\App\Http\Controllers\PostsCommentsController;
use Modules\Post\App\Http\Controllers\PostsCommentsRepliesController;

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

Route::prefix('v1')->name('api.')->group(function () {

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::apiResource('/dashboard/posts', PostAdminController::class)->withTrashed()->names('admin.post');

        Route::post('/dashboard/posts/image', [PostAdminController::class, 'storeImage']);

        Route::apiResource('posts.comments', PostsCommentsController::class)->only(['store'])->scoped(['post' => 'slug']);
        Route::apiResource('posts.comments.replies', PostsCommentsRepliesController::class)->only(['store'])->scoped(['post' => 'slug']);
    });

    Route::apiResource('posts', PostController::class)->scoped(['post' => 'slug'])->only(['index', 'show']);
    Route::apiResource('posts.comments', PostsCommentsController::class)->only(['index'])->scoped(['post' => 'slug']);

    Route::controller(PostController::class)->name('post.')->group(function () {

        Route::get('/post/featureds', 'featured')->name('featured');

        Route::get('/post/latest', 'latest')->name('latest');

        Route::get('/post/search', 'search')->name('search');
    });
});
