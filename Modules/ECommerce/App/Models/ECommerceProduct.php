<?php

namespace Modules\ECommerce\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ECommerce\Database\factories\ECommerceProductFactory;

class ECommerceProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ECommerceProductFactory
    {
        return ECommerceProductFactory::new();
    }
}
