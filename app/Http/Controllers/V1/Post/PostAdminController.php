<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\V1\Post\PostRequest;
use App\Http\Resources\V1\Post\PostCollection;
use App\Http\Resources\V1\Post\PostResource;
use App\Models\Post;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters([AllowedFilter::exact('publish')])
            ->allowedSorts('created_at')
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
            ->with(['author', 'cover'])
            ->withCount(['comments as total_comments'])->paginate(10);

        return response()->json(['posts' => new PostCollection($posts)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateRequest($request);

        // https://gemini.google.com/app/8baed29baf267599 to syncTags

        $post = Post::create($data);

        $this->handleCoverImage($post, $request);

        $post->load(['author', 'cover', 'tags']);

        return response()->json(['post' => $post]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $post = Post::where('id', $id)->with(['author', 'tags', 'cover'])->withCount(['comments'])->firstOrFail();

        $post->comments = $post->comments()->with('user', 'replies')->paginate(10); // 10 comments per page

        // $post->tags = $post->tags();

        return response()->json(['post' => new PostResource($post)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        try {
            $post->update($request->validated());

            if ($request->hasFile('cover')) {
                $this->handleCoverImage($post, $request);
            }

            $post->load(['author', 'cover', 'tags']);

            return response()->json($request->all());
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        if ($post->cover) {
            Storage::delete($post->cover->path);
            $post->cover->delete();
        }

        $post->delete();

        return response()->json(['message' => 'deleted']);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'description' => 'required|string',
            'duration' => 'required|integer',
            'content' => 'required|string',
            'title' => 'required|string',
            'publish' => 'boolean',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id', // Validate each tag ID exists
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust as needed
        ]);
    }

    private function handleCoverImage(Post $post, Request $request): void
    {
        if (!$request->hasFile('cover')) {
            return;  // No cover uploaded, do nothing
        }

        $cover = $request->file('cover');

        $fileName = $this->generateUniqueFileName($cover);
        $path = "uploads/post/cover/$fileName";

        if (extension_loaded('imagick')) {
            $this->storeResizedImage($cover, $path);
        } else {
            $cover->storeAs($path, $fileName);
        }

        $post->cover()->updateOrCreate(['path' => $path]);
    }

    private function generateUniqueFileName(UploadedFile $cover): string
    {
        $extension = $cover->getClientOriginalExtension();
        $originalName = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
        return $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;
    }

    private function storeResizedImage(UploadedFile $cover, string $path): void
    {
        $image = \Intervention\Image\Laravel\Facades\Image::make($cover->getPathname())
            ->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($cover->getClientOriginalExtension(), 75);

        Storage::put($path, (string) $image);
    }
}
