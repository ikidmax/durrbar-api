<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Review;

trait ReviewableRateable
{
    /**
     * Return the reviews relationship.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function ratings(): array
    {
        $ratings = [];

        for ($i = 5; $i >= 1; $i--) {
            $ratings[] = [
                'name' => $i . 'star',
                'count' => $this->reviews()->where('rating', '=', $i)->count()
            ];
        }

        return $ratings;
    }

    /**
     * Calculate the average rating.
     */
    public function averageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0.0;
    }
}
