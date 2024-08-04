<?php

namespace App\Http\Controllers\V1\ECommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ECommerce\ECommerceProduct;

class ProductsCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ECommerceProduct $post): JsonResponse
    {
        $comments = $post->comments()->latest()->with('user', 'replies.user')->paginate(10); // 10 comments per page

        return response()->json(['comments' => $comments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ECommerceProduct $post): JsonResponse
    {
        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['comment' => $comment]);
    }
}
