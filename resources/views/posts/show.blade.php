@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Main Content --}}
    <article class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" 
                     alt="{{ $post->title }}"
                     class="w-full h-96 object-cover">
            @endif
            
            <div class="p-8">
                {{-- Meta Info --}}
                <div class="flex items-center gap-4 mb-4 flex-wrap">
                    <a href="{{ route('categories.show', $post->category->slug) }}" 
                       class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm hover:bg-blue-200 transition">
                        <i class="fas fa-folder"></i> {{ $post->category->name }}
                    </a>
                    <span class="text-gray-500 text-sm">
                        <i class="far fa-calendar"></i> {{ $post->formatted_date }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        <i class="far fa-clock"></i> {{ $post->reading_time }} min read
                    </span>
                    <span class="text-gray-500 text-sm">
                        <i class="far fa-eye"></i> {{ $post->views }} views
                    </span>
                </div>
                
                {{-- Title --}}
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>
                
                {{-- Author Info --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                    <i class="fas fa-user-circle text-4xl text-gray-400"></i>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $post->user->name }}</p>
                        <p class="text-sm text-gray-500">Posted {{ $post->published_at->diffForHumans() }}</p>
                    </div>
                </div>
                
                {{-- Excerpt --}}
                @if($post->excerpt)
                    <p class="text-xl text-gray-700 mb-6 italic border-l-4 border-blue-500 pl-4 bg-blue-50 p-4 rounded">
                        {{ $post->excerpt }}
                    </p>
                @endif
                
                {{-- Content --}}
                <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($post->body)) !!}
                </div>
                
                {{-- Tags --}}
                @if($post->tags->count())
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">
                            <i class="fas fa-tags"></i> Tags
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}" 
                                   class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-3 py-1 rounded-full text-sm transition">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- Action Buttons (Owner Only) --}}
                @can('update', $post)
                    <div class="mt-8 pt-6 border-t flex gap-4">
                        <a href="{{ route('posts.edit', $post) }}" 
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                            <i class="fas fa-edit mr-2"></i> Edit Post
                        </a>
                        
                        <form method="POST" action="{{ route('posts.destroy', $post) }}"
                              onsubmit="return confirm('Are you sure you want to delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition inline-flex items-center">
                                <i class="fas fa-trash mr-2"></i> Delete Post
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>

        {{-- Comments Section --}}
        <div class="mt-8 bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="far fa-comments"></i> Comments ({{ $post->approvedComments->count() }})
            </h2>

            {{-- Comment Form --}}
            @auth
                <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-8">
                    @csrf
                    <textarea name="body" rows="4" 
                              placeholder="Write your comment..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" 
                            class="mt-3 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </form>
            @else
                <div class="bg-gray-100 rounded-lg p-6 mb-8 text-center">
                    <p class="text-gray-600 mb-3">Please login to leave a comment</p>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Login here
                    </a>
                </div>
            @endauth

            {{-- Display Comments --}}
            @forelse($post->approvedComments->where('parent_id', null) as $comment)
                <div class="mb-6 border-l-2 border-gray-200 pl-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-user-circle text-3xl text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                    @can('delete', $comment)
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                              onsubmit="return confirm('Delete this comment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                                <p class="text-gray-700">{{ $comment->body }}</p>
                            </div>
                            
                            {{-- Replies --}}
                            @if($comment->replies->where('is_approved', true)->count())
                                <div class="mt-4 ml-6 space-y-4">
                                    @foreach($comment->replies->where('is_approved', true) as $reply)
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-user-circle text-2xl text-gray-300"></i>
                                            <div class="flex-1 bg-gray-100 rounded-lg p-3">
                                                <div class="flex justify-between items-start mb-1">
                                                    <div>
                                                        <p class="font-semibold text-sm text-gray-900">{{ $reply->user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    @can('delete', $reply)
                                                        <form method="POST" action="{{ route('comments.destroy', $reply) }}"
                                                              onsubmit="return confirm('Delete this reply?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                                <p class="text-sm text-gray-700">{{ $reply->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">
                    <i class="far fa-comment-slash text-3xl mb-2 block text-gray-300"></i>
                    No comments yet. Be the first to comment!
                </p>
            @endforelse
        </div>
    </article>

    {{-- Sidebar --}}
    <aside class="lg:col-span-1">
        {{-- Author Card --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-user"></i> About Author
            </h3>
            <div class="text-center">
                <i class="fas fa-user-circle text-6xl text-gray-400 mb-3"></i>
                <p class="font-semibold text-gray-900">{{ $post->user->name }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    {{ $post->user->posts()->published()->count() }} posts published
                </p>
            </div>
        </div>

        {{-- Share Widget --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-share-alt"></i> Share this post
            </h3>
            <div class="flex gap-2">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('posts.show', $post)) }}" 
                   target="_blank"
                   class="flex-1 bg-blue-400 text-white py-2