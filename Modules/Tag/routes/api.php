<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Tag\App\Http\Controllers\TagController;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.dashboard.')->group(function () {
    Route::apiResource('dashboard/tag', TagController::class);
});
