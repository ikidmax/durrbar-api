<?php

namespace App\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\User\App\Http\Requests\ProfilePhotoRequest;
use App\Actions\Profile\DeleteProfilePhoto;
use App\Actions\Profile\UpdateProfilePhoto;
use App\Http\Responses\V1\User\DeletePhotoResponse;
use App\Http\Responses\V1\User\UpdatePhotoResponse;


class ProfilePhotoController extends Controller
{
    /**
     * Update the user's profile photo.
     *
     * @param  \Modules\User\App\Http\Requests\ProfilePhotoRequest  $request
     * @param  \Modules\User\App\Actions\Profile\UpdateProfilePhoto  $updater
     * @return \Modules\User\App\Http\Responses\UpdatePhotoResponse
     */
    public function update(ProfilePhotoRequest $request, UpdateProfilePhoto $updater): UpdatePhotoResponse
    {
        $updater->update($request->user(), $request->all());

        return app(UpdatePhotoResponse::class);
    }

    /**
     * Delete the user's profile photo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\User\App\Actions\Profile\DeleteProfilePhoto  $updater
     * @return \Modules\User\App\Http\Responses\DeletePhotoResponse
     */
    public function delete(Request $request, DeleteProfilePhoto $updater): DeletePhotoResponse
    {
        $updater->delete($request->user());

        return app(DeletePhotoResponse::class);
    }
}
