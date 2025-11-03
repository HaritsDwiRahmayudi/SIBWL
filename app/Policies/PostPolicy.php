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
        return $user && $user->id === $post->user_id;
    }

    public function create(User $user): bool
    {
        return true; // Semua authenticated user bisa create
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}