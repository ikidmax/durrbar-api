<?php

namespace Modules\User\App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    /**
    * Update the user's profile photo.
    *
    * @param \Illuminate\Http\UploadedFile $photo
    * @return void
    */
    public function updateProfilePhoto(UploadedFile $photo)
    {
        tap($this->photo, function ($previous) use ($photo) {
            // Define custom name for avatar
            $extension = $photo->getClientOriginalExtension();
            $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;
            // Upload & save photo
            $this->forceFill([
                'photo' => $photo->storePubliclyAS(
                    '/uploads/user/avater', $fileName, ['disk' => $this->profilePhotoDisk()]
                ),
            ])->save();
            // Delete if avatar exist
            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
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

        Storage::disk($this->profilePhotoDisk())->delete($this->photo);

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
         ? Storage::disk($this->profilePhotoDisk())->url($this->photo)
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
        
        return 'https://ui-avatars.com/api/?name='.urlencode($name). '&color=7F9CF5&background=EBF4FF';
    }
    
    /**
    * Get the disk that profile photos should be stored on.
    *  
    * @return string
    */

    protected function profilePhotoDisk()
    {
        return isset($_ENV['FILESYSTEM_DISK']) ? $_ENV['FILESYSTEM_DISK'] : config('fortify.profile_photo_disk', 'public');
    }
}