<?php

namespace Modules\User\App\Actions\Profile;

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\User\App\Contracts\UpdatesUserPhoto;

class UpdateProfilePhoto implements UpdatesUserPhoto
{
    /**
     * Validate and update the given user's photo.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'photo' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validate();
 
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }
    
    }
}