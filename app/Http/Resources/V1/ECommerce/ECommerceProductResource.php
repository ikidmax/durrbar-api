<?php

namespace App\Http\Resources\V1\ECommerce;

use App\Http\Resources\V1\Image\ImageResource;
use App\Http\Resources\V1\Tag\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ECommerceProductResource extends JsonResource
{
    function generateReviews(int $count = 8): array
    {
        $attachments = [
            'http://localhost:8000/storage/uploads/product/image/product-1.webp',
            'http://localhost:8000/storage/uploads/product/image/product-2.webp',
            'http://localhost:8000/storage/uploads/product/image/product-3.webp',
            'http://localhost:8000/storage/uploads/product/image/product-4.webp',
            'http://localhost:8000/storage/uploads/product/image/product-5.webp',
            'http://localhost:8000/storage/uploads/product/image/product-6.webp',
            'http://localhost:8000/storage/uploads/product/image/product-7.webp',
            'http://localhost:8000/storage/uploads/product/image/product-8.webp'
        ];

        return collect(range(0, $count - 1))->map(function ($index) use ($attachments) {
            return [
                'id' => uniqid('mock_id_' . $index . '_'),
                'name' => "Full Name $index",
                'postedAt' => now()->subDays($index)->toDateTimeString(),
                'comment' => "This is a mock sentence for review $index.",
                'isPurchased' => (bool)rand(0, 1),
                'rating' => round(0.5 + mt_rand() / mt_getrandmax() * (5.0 - 0.5), 2),
                'avatarUrl' => "http://localhost:8000/storage/uploads/user/avatar/402983227_1060150378742549_9108669481637609166_n_1722140045_66a5c58d80148.jpg",
                'helpful' => rand(0, 1000),
                'attachments' => $this->getReviewAttachments($index, $attachments),
            ];
        })->toArray();
    }

    private function getReviewAttachments(int $index, array $attachments): array
    {
        return ($index === 1 ? array_slice($attachments, 0, 1) : []) +
            ($index === 3 ? array_slice($attachments, 2, 4) : []) +
            ($index === 5 ? array_slice($attachments, 5, 8) : []);
    }

    public function calculateAverageRating(array $reviews): float
    {
        return collect($reviews)->avg('rating') ?? 0.0; // Using collections
    }

    function ratings(array $reviews): array
    {
        $ratings = [];

        // Initialize the counts for each star rating (from 5 stars to 1 star)
        for ($i = 5; $i >= 1; $i--) {
            $ratings[$i] = [
                'name' => $i . 'star',
                'count' => 0,
            ];
        }

        // Count the number of reviews for each star rating
        foreach ($reviews as $review) {
            $rating = (int)floor($review['rating']); // Convert float rating to integer

            if (isset($ratings[$rating])) {
                $ratings[$rating]['count']++;
            }
        }

        // Reindex the array to match the desired output format (from 5 stars to 1 star)
        return array_values($ratings);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reviews = $this->generateReviews(8);
        $averageRating = $this->calculateAverageRating($reviews);
        $ratings = $this->ratings($reviews);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => 'hvyhufu',
            'sku' => $this->sku,
            'price' => $this->price,
            'taxes' => $this->taxes,
            'publish' => $this->publish,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'available' => $this->available,
            'totalSold' => $this->total_sold,
            'priceSale' => $this->price_sale,
            'description' => $this->description,
            'inventoryType' => $this->inventory_type,
            'subDescription' => $this->sub_description,
            'createdAt' => $this->whenHas('created_at'),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => ImageResource::collection($this->whenLoaded('images')),

            'variants' => [
                'gender' => $this->whenLoaded('variants', function () {
                    return $this->variants->where('type', 'gender')->pluck('name');
                }),
                'colors' => $this->whenLoaded('variants', function () {
                    return $this->variants->where('type', 'color')->pluck('name');
                }),
                'sizes' => $this->whenLoaded('variants', function () {
                    return $this->variants->where('type', 'size')->pluck('name');
                }),
                'memories' => $this->whenLoaded('variants', function () {
                    return $this->variants->where('type', 'memory')->pluck('name');
                }),
            ],

            'coverUrl' => optional($this->whenLoaded('images')->first())->url ?? null,
            // ECommerceColorResource::collection($this->whenLoaded('colors')),
            'ratings' => $ratings,
            'reviews' => $reviews,
            'totalReviews' => 8,
            'totalRatings' => $averageRating,
            'newLabel' => [
                'enabled' => (bool) $this->new_label_enabled && !empty($this->new_label_content),
                'content' => $this->new_label_enabled && !empty($this->new_label_content) ? $this->new_label_content : null,
            ],
            'saleLabel' => [
                'enabled' => (bool) $this->sale_label_enabled && !empty($this->sale_label_content),
                'content' => $this->sale_label_enabled && !empty($this->sale_label_content) ? $this->sale_label_content : null,
            ],
        ];
    }
}
