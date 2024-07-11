<?php

namespace Modules\User\App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    /**
     * Update the user's profile photo.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return void
     */
    public function updateProfilePhoto(UploadedFile $photo, User $user)
    {
        $previousPhoto = $user->photo;

        // Define custom name for avatar
        $extension = $photo->getClientOriginalExtension();
        $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

        if (extension_loaded('imagick')) {
            // Resize and compress the photo using Imagick
            $image = \Intervention\Image\Laravel\Facades\Image::make($photo->getPathname())
                ->resize(null, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($extension, 75); // 75 is the quality percentage

            // Store the image
            $path = 'uploads/user/avatar/' . $fileName;
            Storage::put($path, (string) $image);
        } else {
            // Directly upload the photo without resizing
            $path = $photo->storeAs('uploads/user/avatar', $fileName);
        }

        // Update user's photo path
        $user->photo = $path;
        $user->save();

        // Delete previous photo if it exists
        if ($previousPhoto) {
            Storage::delete($previousPhoto);
        }
    }

    /**
     *Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        // if (! Features::manages Profile Photos()) {
        //    return;
        // }

        if (is_null($this->photo)) {
            return;
        }

        Storage::delete($this->photo);

        $this->forceFill([
            'photo' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo
     * 
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? Storage::url($this->photo)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no phofile photo has been uploaded
     * 
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }
}
