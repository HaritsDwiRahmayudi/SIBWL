@extends('layouts.app')

@section('title', 'All Posts')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-3">
        <div class="mb-6">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                <i class="fas fa-newspaper"></i> Blog Posts
            </h1>
            
            {{-- Search & Filter --}}
            <form method="GET" class="bg-white rounded-lg shadow-md p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search posts by title or content..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request()->hasAny(['search', 'category', 'tag']))
                            <a href="{{ route('posts.index') }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                
                @if(request('category') || request('tag'))
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-sm text-gray-600">Filter:</span>
                        @if(request('category'))
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                Category: {{ request('category') }}
                            </span>
                        @endif
                        @if(request('tag'))
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                Tag: {{ request('tag') }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($posts as $post)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        @if($post->cover_image)
                            <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                <i class="fas fa-image text-white text-4xl opacity-50"></i>
                            </div>
                        @endif
                        
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <a href="{{ route('categories.show', $post->category->slug) }}" 
                                   class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-200 transition">
                                    <i class="fas fa-folder"></i> {{ $post->category->name }}
                                </a>
                                <span class="text-gray-500 text-xs">
                                    <i class="far fa-calendar"></i> {{ $post->published_at->diffForHumans() }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    <i class="far fa-eye"></i> {{ $post->views }}
                                </span>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('posts.show', $post) }}" class="hover:text-blue-600 transition">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->body), 120) }}
                            </p>
                            
                            @if($post->tags->count())
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($post->tags->take(3) as $tag)
                                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-center pt-3 border-t">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user-circle text-gray-400"></i>
                                    <span class="text-sm text-gray-600">{{ $post->user->name }}</span>
                                </div>
                                <a href="{{ route('posts.show', $post) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No posts found.</p>
                @if(request()->hasAny(['search', 'category', 'tag']))
                    <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <aside class="lg:col-span-1">
        {{-- Categories Widget --}}
        <div class="bg-white rounded-lg shadow-md p-5 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-folder text-blue-600 mr-2"></i> Categories
            </h3>
            <ul class="space-y-2">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="flex justify-between items-center text-gray-600 hover:text-blue-600 transition">
                            <span>{{ $category->name }}</span>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                {{ $category->posts_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tags Widget --}}
        <div class="bg-white rounded-lg shadow-md p-5">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tags text-green-600 mr-2"></i> Popular Tags
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($sidebarTags->sortByDesc('posts_count')->take(15) as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}" 
                       class="bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 text-sm px-3 py-1 rounded-full transition">
                        #{{ $tag->name }} ({{ $tag->posts_count }})
                    </a>
                @endforeach
            </div>
        </div>
    </aside>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush