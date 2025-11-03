@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('title', "Posts in Category: " . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Category Header --}}
    <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <i class="fas fa-folder text-blue-600"></i>
            Category: {{ $category->name }}
        </h1>
        @if($category->description)
            <p class="text-lg text-gray-600">{{ $category->description }}</p>
        @endif
    </div>

    {{-- Main Content (List of Posts) --}}
    <div class="lg:col-span-3">
        @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Loop through posts --}}
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
                                {{-- Category badge --}}
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    <i class="fas fa-folder"></i> {{ $post->category->name }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    <i class="far fa-calendar"></i> {{ $post->published_at?->diffForHumans() }}
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
                <p class="text-gray-500 text-lg">No posts found in this category.</p>
                <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                    Back to all posts
                </a>
            </div>
        @endif
    </div>
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