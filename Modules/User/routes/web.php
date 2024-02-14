<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('auth', AuthController::class)->names('auth');
});

Route::domain(env('FRONTEND_URL'))->group(function () {
    Route::get('/auth/verify')->name('verification.notice');
});