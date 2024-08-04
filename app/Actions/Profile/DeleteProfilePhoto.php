<?php

namespace App\Actions\Profile;

use App\Models\User\User;
use App\Contracts\DeleteUserPhoto;

class DeleteProfilePhoto implements DeleteUserPhoto
{
    /**
     * delete the given user's photo.
     *
     * @param  array<string
     */
    public function delete(User $user): void
    {
        $user->deleteProfilePhoto();
    }
}
