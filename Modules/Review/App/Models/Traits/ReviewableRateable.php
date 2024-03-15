<?php

namespace Modules\Review\App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Review\App\Models\Review;

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
        $fiveStar = $this->reviews()->where('rating', '=', 5)->count();
        $fourStar = $this->reviews()->where('rating', '=', 4)->count();
        $threeStar = $this->reviews()->where('rating', '=', 3)->count();
        $twoStar = $this->reviews()->where('rating', '=', 2)->count();
        $oneStar = $this->reviews()->where('rating', '=', 1)->count();

        return [
            ['value' => '5star', 'number' => $fiveStar],
            ['value' => '4star', 'number' => $fourStar],
            ['value' => '3star', 'number' => $threeStar],
            ['value' => '2star', 'number' => $twoStar],
            ['value' => '1star', 'number' => $oneStar],
        ];
    }
}
