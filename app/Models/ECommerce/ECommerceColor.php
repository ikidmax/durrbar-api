<?php

namespace App\Models\ECommerce;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ECommerceColor extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ecommerce_colors';

    protected $fillable = ['label', 'code'];

    public function products(): MorphToMany
    {
        return $this->morphedByMany(ECommerceProduct::class, 'colorable', 'ecommerce_colorables', 'color_id');
    }
}
