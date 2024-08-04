<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\V1\Post\PostCollection;
use App\Http\Resources\V1\Post\PostResource;
use App\Models\Post;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

// use Modules\Tag\App\Models\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = QueryBuilder::for(Post::class)->where('publish', 'published')->allowedFields(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author', 'cover'])->paginate(10);

        return response()->json(['posts' => new PostCollection($posts)]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['author', 'cover', 'tags'])->loadCount(['comments'])->firstOrFail();

        return response()->json(['post' => new PostResource($post)]);
    }

    public function featured(): JsonResponse
    {
        $featureds = Post::where('featured', 1)->select(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author', 'cover'])->withCount(['comments'])->limit(5)->get();
        return response()->json(['featureds' => PostResource::collection($featureds)]);
    }

    public function latest(): JsonResponse
    {
        $latest = Post::select(
            'id',
            'slug',
            'title',
            'duration',
            'author_id',
            'created_at',
            'total_views',
            'total_shares',
            'description',
        )->with(['author', 'cover'])->withCount(['comments'])->limit(5)->get();
        return response()->json(['latest' => PostResource::collection($latest)]);
    }

    public function search(Request $request): JsonResponse
    {
        // Retrieve the query parameter from the request
        $query = $request->query('query');

        $results = (new Search())
            ->registerModel(Post::class, function (ModelSearchAspect $modelSearchAspect) {
                $modelSearchAspect
                    ->addSearchableAttribute('title')
                    ->with('cover');
            })
            ->search($query);

        $newres = [];

        foreach ($results as $result) {
            $newres[] = $result->searchable;
        }

        return response()->json(['results' => $newres]);
    }
}
