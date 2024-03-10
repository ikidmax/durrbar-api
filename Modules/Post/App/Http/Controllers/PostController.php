<?php

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Post\App\Http\Requests\PostRequest;
use Modules\Post\App\Http\Resources\PostResource;
use Modules\Post\App\Models\Post;

class PostController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = Post::with(['author', 'comments', 'tags'])->withCount(['comments as total_comments'])->paginate(19);

        return response()->json(['posts' => $posts]);
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
        $post = Post::where('id', $id)->with(['author'])->withCount(['comments as total_comments'])->first();
        $post->comments = $post->comments()->with('user', 'children')->paginate(5); // 10 comments per page

        $post->tags = ['Technology', 'Marketing', 'Design', 'Photography', 'Art'];

        return response()->json(['post' => $post]);
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
