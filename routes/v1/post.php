<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Post\PostAdminController;
use App\Http\Controllers\V1\Post\PostController;
use App\Http\Controllers\V1\Post\PostsCommentsController;
use App\Http\Controllers\V1\Post\PostsCommentsRepliesController;

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



Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('/dashboard/posts', PostAdminController::class)->withTrashed()->names('posts.admin');

    Route::post('/dashboard/posts/image', [PostAdminController::class, 'storeImage']);

    Route::apiResource('posts.comments', PostsCommentsController::class)->only(['store'])->scoped(['post' => 'slug']);
    Route::apiResource('posts.comments.replies', PostsCommentsRepliesController::class)->only(['store'])->scoped(['post' => 'slug']);
});

Route::controller(PostController::class)->name('posts.')->group(function () {

    Route::get('/posts/featureds', 'featured')->name('featured');

    Route::get('/posts/latest', 'latest')->name('latest');

    Route::get('/posts/search', 'search')->name('search');
});

Route::apiResource('posts', PostController::class)->scoped(['post' => 'slug'])->only(['index', 'show']);

Route::apiResource('posts.comments', PostsCommentsController::class)->only(['index'])->scoped(['post' => 'slug']);
