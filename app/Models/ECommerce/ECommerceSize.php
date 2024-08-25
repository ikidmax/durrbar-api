<?php

namespace App\Models\ECommerce;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ECommerceSize extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ecommerce_sizes';

    protected $fillable = ['size'];

    public function sizeable(): MorphTo
    {
        return $this->morphTo();
    }
}
