<?php

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Post\App\Http\Requests\PostRequest;
use Modules\Post\App\Http\Resources\PostCollection;
use Modules\Post\App\Http\Resources\PostResource;
use Modules\Post\App\Models\Post;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\Search;
use Spatie\Tags\Tag;

// use Modules\Tag\App\Models\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
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

        return response()->json(['posts' => $posts]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['author', 'cover', 'tags'])->loadCount(['comments as total_comments'])->firstOrFail();

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
        )->with(['author', 'cover'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['featureds' => $featureds]);
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
        )->with(['author', 'cover'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['latest' => $latest]);
    }

    public function search(Request $request): JsonResponse
    {
        // Retrieve the query parameter from the request
        $query = $request->query('query');

        $results = (new Search())
            ->registerModel(Post::class, 'title')
            ->search($query);

        $newres = [];

        foreach ($results as $result) {
            $newres[] = $result->searchable;
        }

        return response()->json(['results' => $results]);
    }
}
