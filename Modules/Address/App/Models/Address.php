<?php

namespace Modules\Address\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Address\Database\factories\AddressFactory;

class Address extends Model
{
    use HasFactory, HasUuids;

    protected $table = "addresses";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'zip_code',
        'address',
        'address_type',
    ];

    // protected static function newFactory(): AddressFactory
    // {
    //     //return AddressFactory::new();
    // }

    /**
     * Get the parent reviewable model (product or post).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
