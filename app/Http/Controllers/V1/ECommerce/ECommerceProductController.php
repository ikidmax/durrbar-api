<?php

namespace App\Http\Controllers\V1\ECommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ECommerce\App\Http\Requests\ECommerceProductRequest;
use Modules\ECommerce\App\Http\Resources\ECommerceProductCollection;
use Modules\ECommerce\App\Http\Resources\ECommerceProductResource;
use Modules\ECommerce\App\Models\ECommerceProduct;
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
        $products = QueryBuilder::for(ECommerceProduct::class)->where('publish', 'published')->allowedFields(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author', 'cover'])->paginate(10);

        return response()->json(['Products' => $products]);
    }

    /**
     * Show the specified resource.
     */
    public function show(ECommerceProduct $product): JsonResponse
    {
        $product->load(['author', 'cover', 'tags'])->loadCount(['comments as total_comments'])->firstOrFail();

        return response()->json(['Product' => new ECommerceProductResource($product)]);
    }

    public function featured(): JsonResponse
    {
        $featureds = ECommerceProduct::where('featured', 1)->select(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author', 'cover'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['featureds' => $featureds]);
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
