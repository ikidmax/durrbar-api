<?php

namespace App\Traits;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Model;

trait HasVariants
{
    /**
     * Sync the given variants with the given model.
     *
     * @param Model $model
     * @param array $variants
     * @return array The IDs of the synced variants.
     */
    public function syncVariants(Model $model, array $variants = []): array
    {
        $syncData = [];

        // Collect all existing variant names to minimize DB calls
        $existingVariants = Variant::whereIn('name', array_merge(...array_values($variants)))
            ->pluck('id', 'name')
            ->toArray();

        foreach ($variants as $type => $values) {
            foreach ($values as $value) {
                // Use the existing variant ID if it exists, or create a new one
                $variantId = $existingVariants[$value] ?? null;

                if (!$variantId) {
                    $variant = Variant::create(['name' => $value, 'type' => $type]);
                    $variantId = $variant->id;
                }

                // Store the variant ID in the sync array
                $syncData[] = $variantId;
            }
        }

        // Sync the variants to the model
        $model->variants()->sync($syncData);

        return $syncData; // Optional: return the synced variant IDs
    }
}
