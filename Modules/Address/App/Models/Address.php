<?php

namespace Modules\Address\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Address\Database\Factories\AddressFactory;

use Modules\User\App\Models\User;

class Address extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'addresses';

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

    protected static function newFactory(): AddressFactory
    {
        return AddressFactory::new();
    }

    /**
     * Get the parent reviewable model (product or post).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
