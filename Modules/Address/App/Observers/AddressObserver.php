<?php

namespace Modules\Address\App\Observers;

use Modules\Address\App\Models\Address;

class AddressObserver
{
    /**
     * Handle the Address "creating" event.
     *
     * @return void
     */
    public function creating(Address $address)
    {
        $this->ensureOnlyOnePrimary($address);
    }

    /**
     * Handle the Address "updating" event.
     *
     * @return void
     */
    public function updating(Address $address)
    {
        $this->ensureOnlyOnePrimary($address);
    }

    /**
     * Ensures that only one default shipping address exists.
     *
     * @param  Address  $address  The address that will be saved.
     */
    protected function ensureOnlyOnePrimary(Address $address): void
    {
        if ($address->primary) {
            $address = Address::query()->whereAddressableId(auth()->user()->id)->where('id', '!=', $address->id)->wherePrimary(true)->first();

            if ($address) {
                $address->primary = false;
                $address->saveQuietly();
            }
        }
    }
}
