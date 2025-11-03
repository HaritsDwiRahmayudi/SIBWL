<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = $category->posts()
            ->with(['user', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(12);

        return view('categories.show', compact('category', 'posts'));
    }
}