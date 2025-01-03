<?php

namespace App\Models\ECommerce;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ECommerceSize extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ecommerce_sizes';

    protected $fillable = ['size'];

    public function products(): MorphToMany
    {
        return $this->morphedByMany(ECommerceProduct::class, 'sizeable', 'ecommerce_sizeables', 'size_id');
    }
}
