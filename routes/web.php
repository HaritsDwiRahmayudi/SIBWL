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

// == RUTE PUBLIK ==
// (Bisa diakses siapa saja)

// Home redirect
Route::redirect('/', '/posts');

// Tampilkan semua post (index) dan satu post (show)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Tampilkan post berdasarkan kategori
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


// == RUTE TERPROTEKSI ==
// (Harus login DAN verifikasi email)
Route::middleware(['auth', 'verified'])->group(function () {

    // Rute Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute Profil (Sekarang sudah 'verified')
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute CRUD Posts (Create, Store, Edit, Update, Delete)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Rute Komentar
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // CATATAN: Rute untuk Kategori dan Tag (jika ada CRUD) juga harus masuk ke sini
    // Contoh: Route::resource('categories', CategoryController::class)->except(['show']);
    // Contoh: Route::resource('tags', TagController::class);
});


// Rute Autentikasi Bawaan Breeze
require __DIR__.'/auth.php';