<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home - redirect to posts
Route::redirect('/', '/posts');

// Public route (Index)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// -----------------------------------------------------------------
// PERUBAHAN UTAMA:
// Semua route yang spesifik dan dilindungi (auth, verified)
// HARUS diletakkan SEBELUM route wildcard (seperti 'posts/{post}').
// -----------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Protected routes - Posts CRUD
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create'); // <-- Sekarang ini dibaca sebelum '/posts/{post}'
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Protected routes - Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});


// -----------------------------------------------------------------
// Route wildcard (seperti /posts/{post}) diletakkan di bawah
// setelah semua route spesifik '/posts/...' didefinisikan.
// -----------------------------------------------------------------
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Dashboard route (untuk redirect setelah login/register)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Auth routes
require __DIR__.'/auth.php';