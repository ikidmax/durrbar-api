<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Address\AddressController;

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

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::apiResource('address', AddressController::class)->names('address');
});
