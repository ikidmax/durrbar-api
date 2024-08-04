<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Address\App\Models\Address;

trait HasAddresses
{
    /**
     * Return the addresses relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
