<?php

namespace Modules\Image\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Image\Database\factories\ImageFactory;

class Image extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'images';

    protected $appends = ['url'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['path'];

    /**
     * Get the URL to the photo
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        return  Storage::url($this->path);
    }

    protected static function newFactory(): ImageFactory
    {
        return ImageFactory::new();
    }

    /**
     * Get the parent imageable model (user or post).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
