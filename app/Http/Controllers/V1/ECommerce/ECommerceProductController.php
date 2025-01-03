<?php

namespace App\Http\Controllers\V1\ECommerce;

use App\Http\Controllers\BaseController;
use App\Http\Requests\V1\ECommerce\ProductRequest;
use App\Http\Resources\V1\ECommerce\ECommerceProductCollection;
use App\Http\Resources\V1\ECommerce\ECommerceProductResource;
use App\Models\ECommerce\ECommerceProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\Search;
use App\Traits\HasVariants;
use Illuminate\Http\UploadedFile;

class ECommerceProductController extends BaseController
{
    use HasVariants;

    private const CACHE_PUBLIC_PRODUCTS = 'public_products_';
    private const CACHE_ADMIN_PRODUCTS = 'admin_products_';
    private const CACHE_FEATURED_PRODUCTS = 'featured_products';
    private const CACHE_LATEST_PRODUCTS = 'latest_products';

    private const ERROR_CREATE = 'Failed to create product';
    private const ERROR_UPDATE = 'Failed to update product';
    private const ERROR_DELETE = 'Failed to delete product';
    private const ERROR_FEATURED = 'Failed to retrieve featured products';
    private const ERROR_LATEST = 'Failed to retrieve latest products';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $isAdmin = $request->is('api/v1/dashboard/products*');
        $cacheKey = $isAdmin ? self::CACHE_ADMIN_PRODUCTS . $request->query('page', 1) : self::CACHE_PUBLIC_PRODUCTS . $request->query('page', 1);
        $cacheDuration = now()->addMinutes(config('cache.durations'));

        $products = Cache::remember($cacheKey, $cacheDuration, function () use ($isAdmin) {
            $query = QueryBuilder::for(ECommerceProduct::class)
                ->allowedFields(
                    'id',
                    'slug',
                    'title',
                    'duration',
                    'author_id',
                    'created_at',
                    'total_views',
                    'total_shares',
                    'created_at'
                )
                ->with(['images']);

            if ($isAdmin) {
                $query->allowedFilters([AllowedFilter::exact('publish')])
                    ->allowedSorts('created_at');
            } else {
                $query->where('publish', 'published');
            }

            return $query->paginate(10);
        });

        return response()->json(['products' => new ECommerceProductCollection($products)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = DB::transaction(function () use ($request) {
                // Create the product
                $product = ECommerceProduct::create($request->validated());

                // Handle variants
                $this->syncVariants($product, $request->input('variants', []));

                // Handle images
                $this->handleProductImages($product, $request);

                return $this->loadProductRelations($product);
            });

            $this->clearProductCache();

            return response()->json(['product' => $product, 'message' => 'Product created successfully!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_CREATE . ': ' . $e->getMessage(), $request);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show(ECommerceProduct $product): JsonResponse
    {
        $cacheKey = "product_{$product->id}";
        $cacheDuration = now()->addMinutes(config('cache.durations')); // Cache duration for 30 minutes

        $product = Cache::remember($cacheKey, $cacheDuration, function () use ($product) {
            // Load relations only if the product is not found in the cache
            return $product->load(['variants', 'tags', 'images']);
        });

        return response()->json(['product' => new ECommerceProductResource($product)]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, ECommerceProduct $product): JsonResponse
    {
        try {
            $product = DB::transaction(function () use ($request, $product) {
                // Update the product
                $product->update($request->validated());

                // Handle variants
                $this->syncVariants($product, $request->input('variants', []));

                // Handle images
                $this->handleProductImages($product, $request);

                return $this->loadProductRelations($product);
            });

            Cache::forget("product_{$product->id}");

            return response()->json(['product' => new ECommerceProductResource($product)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_UPDATE . ': ' . $e->getMessage(), $request);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ECommerceProduct $product): JsonResponse
    {
        try {
            DB::transaction(function () use ($product) {
                // Delete associated images
                foreach ($product->images as $image) {
                    if (Storage::exists($image->path)) {
                        Storage::delete($image->path);
                    }
                    $image->delete();
                }

                // Delete the product
                $product->delete();
            });

            Cache::forget("product_{$product->id}");

            return response()->json(['message' => 'Product deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_DELETE . ': ' . $e->getMessage(), null);
        }
    }

    public function featured(): JsonResponse
    {
        try {
            $featureds = Cache::remember(self::CACHE_FEATURED_PRODUCTS, 60 * 60, function () {
                return ECommerceProduct::with(['images'])->limit(6)->get();
            });

            return response()->json(['featureds' => ECommerceProductResource::collection($featureds)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_FEATURED . ': ' . $e->getMessage(), null);
        }
    }

    public function latest(): JsonResponse
    {
        try {
            $latest = Cache::remember(self::CACHE_LATEST_PRODUCTS, 60 * 60, function () {
                return ECommerceProduct::with(['author', 'cover'])
                    ->withCount(['comments as total_comments'])
                    ->limit(5)->get();
            });
            return response()->json(['latest' => $latest]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_LATEST . ': ' . $e->getMessage(), null);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');

        $results = (new Search())
            ->registerModel(ECommerceProduct::class, 'title')
            ->search($query);

        $formattedResults = ECommerceProductResource::collection(collect($results)->pluck('searchable'));

        return response()->json(['results' => $formattedResults]);
    }

    private function loadProductRelations(ECommerceProduct $product): ECommerceProduct
    {
        return $product->load(['variants', 'tags', 'images']);
    }

    private function handleProductImages(ECommerceProduct $product, Request $request): void
    {
        $existingImages = $product->images()->get();

        $incomingUrls = $this->filterIncomingImages($request->input('images'), true);
        $incomingFiles = $this->filterIncomingImages($request->file('images'), false);

        $this->deleteOldImages($existingImages, $incomingUrls);
        $this->processNewImageFiles($product, $incomingFiles);
    }

    private function filterIncomingImages($images, bool $isUrl): \Illuminate\Support\Collection
    {
        return collect($images)->filter(function ($image) use ($isUrl) {
            return $isUrl ? is_string($image) : $image instanceof UploadedFile;
        });
    }

    private function deleteOldImages($existingImages, $incomingUrls): void
    {
        foreach ($existingImages as $existingImage) {
            if (!$incomingUrls->contains($existingImage->url)) {
                Storage::delete($existingImage->path);
                $existingImage->delete();
            }
        }
    }

    private function processNewImageFiles(ECommerceProduct $product, $incomingFiles): void
    {
        foreach ($incomingFiles as $file) {
            $fileName = $this->generateUniqueFileName($file);
            $path = "uploads/product/images/$fileName";

            if (extension_loaded('imagick')) {
                $this->storeResizedImage($file, $path);
            } else {
                $file->storeAs('uploads/product/images', $fileName);
            }

            $product->images()->create(['path' => $path]);
        }
    }

    private function storeResizedImage(UploadedFile $image, string $path): void
    {
        $directory = dirname($path);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $resizedImage = \Intervention\Image\Laravel\Facades\Image::make($image->getPathname())
            ->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($image->getClientOriginalExtension(), 75);

        Storage::put($path, (string) $resizedImage);
    }

    private function clearProductCache(): void
    {
        Cache::forget(self::CACHE_ADMIN_PRODUCTS);
        Cache::forget(self::CACHE_PUBLIC_PRODUCTS);
    }
}
