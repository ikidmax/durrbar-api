<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\BaseController;
use App\Http\Requests\V1\Post\PostRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\V1\Post\PostCollection;
use App\Http\Resources\V1\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;

class PostController extends BaseController
{
    private const CACHE_PUBLIC_POSTS = 'public_posts_';
    private const CACHE_ADMIN_POSTS = 'admin_posts_';
    private const CACHE_FEATURED_POSTS = 'featured_posts';
    private const CACHE_LATEST_POSTS = 'latest_posts';

    // Error messages
    private const ERROR_CREATE = 'Failed to create post';
    private const ERROR_UPDATE = 'Failed to update post';
    private const ERROR_DELETE = 'Failed to delete post';
    private const ERROR_FEATURED = 'Failed to retrieve featured posts';
    private const ERROR_LATEST = 'Failed to retrieve latest posts';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $isAdmin = $request->is('api/v1/dashboard/posts*');
        $cacheKey = $isAdmin ? self::CACHE_ADMIN_POSTS . $request->query('page', 1) : self::CACHE_PUBLIC_POSTS . $request->query('page', 1);
        $cacheDuration = now()->addMinutes(10);

        $posts = Cache::remember($cacheKey, $cacheDuration, function () use ($isAdmin) {
            $query = QueryBuilder::for(Post::class)
                ->allowedFields('id', 'slug', 'title', 'author_id', 'created_at', 'total_views', 'total_shares')
                ->with(['author', 'cover']);

            if ($isAdmin) {
                $query->allowedFilters([AllowedFilter::exact('publish')])
                    ->allowedSorts('created_at')
                    ->withCount(['comments as total_comments']);
            } else {
                $query->where('publish', 'published');
            }

            return $query->paginate(10);
        });

        return response()->json(['posts' => new PostCollection($posts)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        try {
            $postData = $request->validated();
            $postData['total_views'] = $postData['total_views'] ?? 0;
            $postData['total_shares'] = $postData['total_shares'] ?? 0;
            $postData['total_favorites'] = $postData['total_favorites'] ?? 0;
            $postData['author_id'] = Auth::id();

            $post = DB::transaction(function () use ($postData, $request) {
                $newPost = Post::create($postData);

                if ($request->hasFile('coverUrl')) {
                    $this->handleCoverImage($newPost, $request);
                }

                return $newPost;
            });

            // Clear cache for all pages (or just the relevant one)
            $this->clearPostCache();

            return response()->json(['post' => new PostResource($post)], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_CREATE . ': ' . $e->getMessage(), $request);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        $cacheKey = "post_{$post->id}";

        $post = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($post) {
            return $this->loadPostRelations($post);
        });

        return response()->json(['post' => new PostResource($post)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        try {
            $post = DB::transaction(function () use ($request, $post) {
                $post->update($request->validated());

                if ($request->hasFile('coverUrl')) {
                    if ($post->cover && Storage::exists($post->cover->path)) {
                        Storage::delete($post->cover->path);
                    }
                    $this->handleCoverImage($post, $request);
                }

                return $this->loadPostRelations($post);
            });

            Cache::forget("post_{$post->id}");

            return response()->json(['post' => new PostResource($post)]);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_UPDATE . ': ' . $e->getMessage(), $request);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        try {
            DB::transaction(function () use ($post) {
                if ($post->cover && Storage::exists($post->cover->path)) {
                    Storage::delete($post->cover->path);
                    $post->cover->delete();
                }

                $post->delete();
            });

            Cache::forget("post_{$post->id}");

            return response()->json(['message' => 'Post deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_DELETE . ': ' . $e->getMessage(), null);
        }
    }

    public function featured(): JsonResponse
    {
        try {
            $featureds = Cache::remember(self::CACHE_FEATURED_POSTS, 60 * 60, function () {
                return Post::where('featured', 1)
                    ->select('id', 'slug', 'title', 'author_id', 'created_at', 'total_views', 'total_shares')
                    ->with(['author', 'cover'])
                    ->withCount(['comments' => function ($query) {
                        $query->whereNull('parent_id');
                    }])
                    ->limit(5)
                    ->get();
            });

            return response()->json(['featureds' => PostResource::collection($featureds)], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_FEATURED . ': ' . $e->getMessage(), null);
        }
    }

    public function latest(): JsonResponse
    {
        try {
            $latest = Cache::remember(self::CACHE_LATEST_POSTS, 60 * 60, function () {
                return Post::select(
                    'id',
                    'slug',
                    'title',
                    'content',
                    'author_id',
                    'created_at',
                    'total_views',
                    'total_shares',
                    'description'
                )
                    ->with(['author', 'cover'])
                    ->withCount(['comments' => function ($query) {
                        $query->whereNull('parent_id');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            });

            return response()->json(['latest' => PostResource::collection($latest)], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleError(self::ERROR_LATEST . ': ' . $e->getMessage(), null);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query');

        // Perform the search and register the Post model with searchable attributes
        $results = (new Search())
            ->registerModel(Post::class, function (ModelSearchAspect $modelSearchAspect) {
                $modelSearchAspect
                    ->addSearchableAttribute('title')
                    ->with('cover'); // Eager load the cover relationship
            })
            ->search($query);

        // Use PostResource to format the search results consistently
        return response()->json(['results' => PostResource::collection(collect($results)->pluck('searchable'))]);
    }


    private function loadPostRelations(Post $post): Post
    {
        return $post->load(['author', 'cover', 'tags'])
            ->loadCount(['comments' => function ($query) {
                $query->whereNull('parent_id');
            }]);
    }

    private function handleCoverImage(Post $post, Request $request): void
    {
        // Existing cover image URL from the database
        $existingCover = $post->cover ? $post->cover->path : null;

        // Incoming cover image could be a URL or a file
        $incomingCover = $request->input('coverUrl'); // URL case
        $incomingCoverFile = $request->file('coverUrl'); // File case

        // If the incoming cover is a URL and matches the existing cover, do nothing
        if (is_string($incomingCover) && $existingCover === $incomingCover) {
            return;
        }

        // Handle if the incoming cover is a new URL (and is different from the existing one)
        if (is_string($incomingCover)) {
            if ($post->cover) {
                Storage::delete($post->cover->path); // Delete the old file if it exists
                $post->cover()->update(['path' => $incomingCover]); // Update with the new URL
            } else {
                $post->cover()->create(['path' => $incomingCover]); // Create a new cover with the URL
            }
            return;
        }

        // Handle if the incoming cover is a file
        if ($incomingCoverFile instanceof UploadedFile) {
            $fileName = $this->generateUniqueFileName($incomingCoverFile);
            $path = "uploads/post/cover/$fileName";

            // Check for Imagick extension for image resizing
            if (extension_loaded('imagick')) {
                $this->storeResizedImage($incomingCoverFile, $path);
            } else {
                $incomingCoverFile->storeAs('uploads/post/cover', $fileName);
            }

            // Update or create a new cover in the database
            if ($post->cover) {
                Storage::delete($post->cover->path); // Delete the old image
                $post->cover()->update(['path' => $path]);
            } else {
                $post->cover()->create(['path' => $path]);
            }
        }
    }


    private function storeResizedImage(UploadedFile $cover, string $path): void
    {
        // Ensure the directory exists
        $directory = dirname($path);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $image = \Intervention\Image\Laravel\Facades\Image::make($cover->getPathname())
            ->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($cover->getClientOriginalExtension(), 75);

        Storage::put($path, (string) $image);
    }

    /**
     * Clear the relevant post cache.
     */
    private function clearPostCache(): void
    {
        Cache::forget(self::CACHE_PUBLIC_POSTS . '*');
        Cache::forget(self::CACHE_ADMIN_POSTS . '*');
    }
}
