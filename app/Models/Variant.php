<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Variant extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'variants';

    protected $fillable = ['name', 'type'];

    public function variantable(): MorphTo
    {
        return $this->morphTo();
    }
}
