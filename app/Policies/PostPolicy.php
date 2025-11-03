<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        // Jika published, semua bisa lihat
        if ($post->isPublished()) {
            return true;
        }
        
        // Jika draft, hanya pemilik yang bisa lihat
        return $user && ($user->isAdmin() || $user->id === $post->user_id);
    }

    public function create(User $user): bool
    {
        // Hanya admin yang bisa create post
        return $user->isAdmin();
    }

    public function update(User $user, Post $post): bool
    {
        // Admin bisa update semua post, user hanya bisa post miliknya
        return $user->isAdmin() || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        // Admin bisa delete semua post, user hanya bisa post miliknya
        return $user->isAdmin() || $user->id === $post->user_id;
    }

    public function restore(User $user, Post $post): bool
    {
        // Admin bisa restore semua post, user hanya bisa post miliknya
        return $user->isAdmin() || $user->id === $post->user_id;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        // Admin bisa force delete semua post, user hanya bisa post miliknya
        return $user->isAdmin() || $user->id === $post->user_id;
    }
}