<?php

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Post\App\Http\Requests\PostRequest;
use Modules\Post\App\Http\Resources\PostCollection;
use Modules\Post\App\Http\Resources\PostResource;
use Modules\Post\App\Models\Post;

class PostController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::select(
            'id',
            'title',
            'duration',
            'cover_url',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author'])->withCount(['comments as total_comments'])->paginate(10);

        return response()->json(['posts' => $posts]);
    }

    public function featured(): JsonResponse
    {
        $featureds = Post::where('featured', 1)->select(
            'id',
            'title',
            'duration',
            'cover_url',
            'author_id',
            'created_at',
            'total_views',
            'total_shares'
        )->with(['author'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['featureds' => $featureds]);
    }

    public function latest(): JsonResponse
    {
        $latest = Post::select(
            'id',
            'title',
            'duration',
            'cover_url',
            'author_id',
            'created_at',
            'total_views',
            'total_shares',
            'description',
        )->with(['author'])->withCount(['comments as total_comments'])->limit(5)->get();
        return response()->json(['latest' => $latest]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        //
        $validated = $request->safe()->except(['id']);

        return response()->json($this->data);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //
        $post = Post::where('id', $id)->with(['author'])->withCount(['comments as total_comments'])->firstOrFail();
        $post->comments = $post->comments()->with('user', 'children')->paginate(5); // 10 comments per page

        $post->tags = ['Technology', 'Marketing', 'Design', 'Photography', 'Art'];

        return response()->json(['post' => new PostResource($post)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, $id): JsonResponse
    {
        //
        // Retrieve the validated input data...
        $validated = $request->validated();

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
