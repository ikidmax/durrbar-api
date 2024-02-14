<?php

namespace Modules\User\App\Actions\Profile;

use Modules\User\App\Models\User;
use Modules\User\App\Contracts\DeleteUserPhoto;

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