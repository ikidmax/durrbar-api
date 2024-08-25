<?php

namespace App\Http\Controllers\V1\ECommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ECommerce\ECommerceProductCollection;
use App\Http\Resources\V1\ECommerce\ECommerceProductResource;
use App\Models\ECommerce\ECommerceProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\Search;
use Spatie\Tags\Tag;

// use Modules\Tag\App\Models\Tag;

class ECommerceProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $products = QueryBuilder::for(ECommerceProduct::class)
            // ->where('publish', 'published')
            ->allowedFields(
                'id',
                'slug',
                'title',
                'duration',
                'author_id',
                'created_at',
                'total_views',
                'total_shares'
            )
            ->with(['images'])
            ->paginate(10);

        return response()->json(['products' => new ECommerceProductCollection($products)]);
    }

    /**
     * Show the specified resource.
     */
    public function show(ECommerceProduct $product): JsonResponse
    {
        $product->load(['images', 'genders'])->firstOrFail();

        return response()->json(['product' => new ECommerceProductResource($product)]);
    }

    public function featured(): JsonResponse
    {
        $featureds = ECommerceProduct::select(
            '*'
        )->with(['images'])->limit(6)->get();

        return response()->json(['featureds' => ECommerceProductResource::collection($featureds)]);
    }

    public function latest(): JsonResponse
    {
        $latest = ECommerceProduct::select(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares',
            'description',
        )->with(['author', 'cover'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['latest' => $latest]);
    }

    public function search(Request $request): JsonResponse
    {
        // Retrieve the query parameter from the request
        $query = $request->query('query');

        $results = (new Search())
            ->registerModel(ECommerceProduct::class, 'title')
            ->search($query);

        $newres = [];

        foreach ($results as $result) {
            $newres[] = $result->searchable;
        }

        return response()->json(['results' => $results]);
    }
}
