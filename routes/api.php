<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::name('api.')->prefix('v1')->group(function () {
    require __DIR__ . '/v1/user.php';
    require __DIR__ . '/v1/address.php';
    require __DIR__ . '/v1/post.php';
});
