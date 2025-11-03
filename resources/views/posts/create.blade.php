@extends('layouts.app')

@section('title', 'Create New Post')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        <i class="fas fa-plus-circle"></i> Create New Post
    </h1>

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Oops! Something went wrong.</strong>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 
      PENTING:
      - action -> route('posts.store') (untuk menyimpan data)
      - method -> POST
      - enctype -> "multipart/form-data" (WAJIB untuk upload gambar)
    --}}
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Title --}}
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500"
                   required>
            @error('title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category --}}
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id"
                    id="category_id"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500"
                    required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Body --}}
        <div class="mb-4">
            <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
            <textarea name="body"
                      id="body"
                      rows="10"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">{{ old('body') }}</textarea>
            @error('body')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tags --}}
        <div class="mb-4">
            <label for="tags" class="block text-sm font-medium text-gray-700">Tags (Hold Ctrl/Cmd to select multiple)</label>
            <select name="tags[]"
                    id="tags"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500"
                    multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ (in_array($tag->id, old('tags', []))) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
            @error('tags')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cover Image --}}
        <div class="mb-4">
            <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image</label>
            <input type="file"
                   name="cover_image"
                   id="cover_image"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
            @error('cover_image')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status"
                    id="status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end">
            <a href="{{ route('posts.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition mr-2">
                Cancel
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save"></i> Save Post
            </button>
        </div>
    </form>
</div>
@endsection