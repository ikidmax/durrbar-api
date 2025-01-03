<?php

namespace App\Http\Controllers\V1\Comment;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\V1\Comment\CommentRequest;
use App\Http\Resources\V1\Comment\CommentResource;
use App\Http\Resources\V1\Comment\CommentCollection;

class CommentController extends Controller
{
    public function __construct()
    {
        // Apply auth middleware only to store, update, and destroy methods
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);

        // Apply a second middleware to the update and destroy methods
        $this->middleware('can:update-comment,comment')->only('update');
        $this->middleware('can:delete-comment,comment')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index($modelType, $modelId, Request $request)
    {
        $model = $this->getModelInstance($modelType, $modelId);

        // Build the base query for comments
        $query = $model->comments()->with(['user', 'comments.user'])->whereNull('parent_id')->orderBy('created_at', 'desc');

        // Paginate the comments for performance, limit nested depth
        $comments = $query->paginate(2);

        // Use CommentCollection for paginated response
        return response()->json(['comments' => new CommentCollection($comments)], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request, $modelType, $modelId): JsonResponse
    {
        $validatedData = $request->validated();

        $model = $this->getModelInstance($modelType, $modelId);

        $comment = $model->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
            'parent_id' => $validatedData['parent_id'] ?? null,
        ]);

        // Return the newly created comment as a resource
        return response()->json(['comment' => new CommentResource($comment->load('user', 'comments.user'))], Response::HTTP_CREATED);
    }

    /**
     * Show the specified resource.
     */
    public function show($modelType, $modelId, Comment $comment): JsonResponse
    {
        // Return a single comment
        return response()->json(['comment' => new CommentResource($comment)], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, $modelType, $modelId, Comment $comment): JsonResponse
    {
        // Ensure the user owns the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $validatedData = $request->validated();

        $comment->update([
            'content' => $validatedData['content'],
        ]);

        // Return updated comment as a resource
        return response()->json(['comment' => new CommentResource($comment->load('user', 'comments'))], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($modelType, $modelId, Comment $comment)
    {
        // Ensure the user owns the comment or has admin rights
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Get model instance based on type.
     */
    private function getModelInstance($modelType, $modelId)
    {
        $modelClass = $this->getModelClass($modelType);

        return $modelClass::findOrFail($modelId);
    }

    /**
     * Map model type to corresponding class.
     */
    private function getModelClass($modelType)
    {
        $models = [
            'posts' => \App\Models\Post::class,
            // Add other model types here as needed
        ];

        if (!array_key_exists($modelType, $models)) {
            abort(Response::HTTP_NOT_FOUND, "Invalid model type");
        }

        return $models[$modelType];
    }
}
