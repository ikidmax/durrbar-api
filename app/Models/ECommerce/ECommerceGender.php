<?php

namespace App\Models\ECommerce;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ECommerceGender extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ecommerce_genders';

    protected $fillable = ['name'];

    public function products(): MorphToMany
    {
        return $this->morphedByMany(ECommerceProduct::class, 'genderable', 'ecommerce_genderables', 'gender_id');
    }
}
