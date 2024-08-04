<?php

use App\Http\Controllers\V1\User\ProfilePhotoController;
use App\Http\Controllers\V1\User\SocialiteController;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\{Request, Response};

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('login')->name('login.')->group(function () {
        Route::get('/callback/{provider}', [SocialiteController::class, 'callback'])->name('callback');
        Route::get('/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('redirect');
    });

    // User routes
    Route::middleware('verified')->prefix('user')->name('user.')->group(function () {

        Route::get('/me', function (Request $request) {
            $user = new UserResource($request->user());
            return response()->json(['user' => $user], Response::HTTP_OK);
        })->name('me');

        // Profile Information...
        Route::post('/profile-photo', [ProfilePhotoController::class, 'update'])->name('photo.update');
        Route::delete('/profile-photo', [ProfilePhotoController::class, 'delete'])->name('photo.delete');
    });
});
