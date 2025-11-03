<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;


class PostController extends Controller
{
    public function index()
    {
     $posts = Post::with(['user', 'category', 'tags'])
        ->published()
        ->search(request('search'))
        ->byCategory(request('category'))
        ->byTag(request('tag'))
        // ------------------------------------------------
        // TAMBAHKAN BARIS INI UNTUK MEMASTIKAN
        // ------------------------------------------------
        ->whereNull('posts.deleted_at') 
        // ------------------------------------------------
        ->latest('published_at')
        ->paginate(12)
        ->withQueryString();

    $categories = Category::withCount('posts')->get();
       $sidebarTags = Tag::withCount('posts')->get();

        return view('posts.index', compact('posts', 'categories', 'sidebarTags'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
       $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('posts/covers', 'public');
        }

        // Set published_at untuk published posts
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);

        // Sync tags (many-to-many)
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post berhasil dibuat!');
    }

    public function show(Post $post)
    {
        Gate::authorize('view', $post);
        
        $post->load(['user', 'category', 'tags', 'approvedComments.user', 'approvedComments.replies']);
        
        // Increment views
        $post->incrementViews();
        
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
        
        // Update slug jika title berubah
        if ($post->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            
            $data['cover_image'] = $request->file('cover_image')
                ->store('posts/covers', 'public');
        }

        // Set published_at ketika status berubah ke published
        if ($data['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        // Sync tags
        $post->tags()->sync($request->tags ?? []); // <-- Gunakan ini

    return redirect()
        ->route('posts.show', $post)
        ->with('success', 'Post berhasil diupdate!');

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post berhasil diupdate!');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        
        // Delete cover image
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete(); // Soft delete

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post berhasil dihapus!');
    }
}