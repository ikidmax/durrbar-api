<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class PostsCommentsRepliesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post, Comment $comment): JsonResponse
    {
        $reply = $comment->replies()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['reply' => $reply]);
    }
}
