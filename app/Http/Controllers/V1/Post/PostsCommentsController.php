<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\V1\Comment\CommentCollection;
use App\Http\Resources\V1\Comment\CommentResource;
use App\Models\Post;

class PostsCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()->latest()->with('user', 'replies.user')->paginate(2); // 10 comments per page

        return response()->json(['comments' => new CommentCollection($comments)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['comment' => $comment]);
    }
}