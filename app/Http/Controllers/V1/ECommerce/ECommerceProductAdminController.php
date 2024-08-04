<?php

namespace App\Http\Controllers\V1\ECommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\ECommerce\App\Models\ECommerceProduct;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ECommerceProductAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = QueryBuilder::for(ECommerceProduct::class)
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

        return response()->json(['posts' => $posts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $post = new ECommerceProduct;

        $tags = $request->input('tags', []);
        $post->description = $request->description;
        $post->duration = $request->duration;
        $post->content = $request->content;
        $post->title = $request->title;
        $post->publish = $request->publish;
        $post->meta_title = $request->meta_title;
        $post->meta_keywords = $request->meta_keywords;
        $post->meta_description = $request->meta_description;
        $post->syncTags($tags);

        $post->save();

        // Delete existing image
        if (isset($request->cover_url)) {
            $coverUrl = json_decode(json_encode($request->cover_url), true);
            $uploadKey = $coverUrl['uploadKey'];
            if (isset($post->cover->path)) {
                if ($post->cover->path !== $uploadKey) {
                    Storage::delete($post->cover->path);
                }
            }

            $post->cover()->updateOrCreate(['path' => $uploadKey]);
        }

        // Fetch the post again with relationships
        $post = $post->load(['author', 'cover', 'tags']);

        return response()->json(['post' => $post]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $post = ECommerceProduct::where('id', $id)->with(['author', 'tags', 'cover'])->withCount(['comments as total_comments'])->firstOrFail();

        $post->comments = $post->comments()->with('user', 'replies')->paginate(10); // 10 comments per page

        // $post->tags = $post->tags();

        return response()->json(['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ECommerceProduct $post): JsonResponse
    {
        $tags = $request->input('tags', []);
        $post->description = $request->description;
        $post->duration = $request->duration;
        $post->content = $request->content;
        $post->title = $request->title;
        $post->publish = $request->publish;
        $post->meta_title = $request->meta_title;
        $post->meta_keywords = $request->meta_keywords;
        $post->meta_description = $request->meta_description;
        $post->syncTags($tags);

        $post->save();

        // Delete existing image
        if (isset($request->cover_url)) {
            $coverUrl = json_decode(json_encode($request->cover_url), true);
            $uploadKey = $coverUrl['uploadKey'];
            if (isset($post->cover->path)) {
                if ($post->cover->path !== $uploadKey) {
                    Storage::delete($post->cover->path);
                    $post->cover()->delete();
                }
            }

            $post->cover()->create(['path' => $uploadKey]);
        }

        // Fetch the post again with relationships
        $post = $post->load(['author', 'cover', 'tags']);

        return response()->json(['post' => $post]);
    }

    public function storeImage(Request $request): JsonResponse
    {
        $extension = $request->file('photo')->getClientOriginalExtension();
        $originalName = pathinfo($request->file('photo')->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

        $path = $request->file('photo')->storePubliclyAS(
            '/uploads/post/cover',
            $fileName
        );

        return response()->json(['path' => $path]);
    }

    public function destroyImage(Request $request): JsonResponse
    {
        if (isset($request->img_path)) {
            Storage::delete($request->img_path);

            return response()->json(['path' => $request->img_path . 'has been deleted']);
        };

        return response()->json(['message' => 'Path not included']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ECommerceProduct $post): JsonResponse
    {
        if ($post->cover) {
            Storage::delete($post->cover->path);
            $post->cover->delete();
        }

        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
