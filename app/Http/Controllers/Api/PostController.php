<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with(['user', 'category', 'tags'])
            ->published()
            ->search($request->search)
            ->latest('published_at')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function show(Post $post): JsonResponse
    {
        if (!$post->isPublished()) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        $post->load(['user', 'category', 'tags', 'approvedComments']);
        $post->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }
}