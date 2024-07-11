<?php

namespace Modules\User\App\Actions\Profile;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\User\App\Contracts\UpdatesUserPhoto;
use Illuminate\Http\UploadedFile;

class UpdateProfilePhoto implements UpdatesUserPhoto
{
    /**
     * Validate and update the given user's photo.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, $file): void
    {
        Validator::make($file, [
            'photo' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validate();

        if (isset($file['photo'])) {
            $user->updateProfilePhoto($file['photo'], $user);
        }
    }
}
