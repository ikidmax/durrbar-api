<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Review\App\Models\Traits\ReviewableRateable;

use Modules\Product\Database\factories\ProductFactory;

class Product extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use ReviewableRateable;

    /**
     * The table associated with the model.
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}