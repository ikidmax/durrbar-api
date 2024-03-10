<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Post\App\Http\Controllers\PostController;

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
    Route::middleware(['auth:sanctum'])->apiResource('posts', PostController::class)->names('post')->except([
        'index', 'show'
    ]);
    Route::apiResource('posts', PostController::class)->names('post')->only([
        'index', 'show'
    ]);
});
