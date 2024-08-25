<?php

namespace App\Models\ECommerce;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ECommerceColor extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ecommerce_colors';

    protected $fillable = ['color'];

    public function colorable(): MorphTo
    {
        return $this->morphTo();
    }
}
