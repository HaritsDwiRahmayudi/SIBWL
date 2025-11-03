<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- TAMBAHKAN INI

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
     
        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.com'], // Kunci unik untuk mencari
            [
                'name' => 'Admin User',     
                'password' => Hash::make('password'), 
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
       
        $users = User::factory(10)->create();
        $allUsers = $users->push($admin);

        // Create categories
        $categories = Category::factory(8)->create();

        // Create tags
        $tags = Tag::factory(20)->create();

        // Create posts
        $categories->each(function ($category) use ($allUsers, $tags) {
            $posts = Post::factory(rand(5, 10))
                ->create([
                    'user_id' => $allUsers->random()->id,
                    'category_id' => $category->id,
                ]);

            // Attach random tags to posts (many-to-many)
            $posts->each(function ($post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(rand(1, 4))->pluck('id')->toArray()
                );
            });
        });

        // Create comments for published posts
        Post::published()->get()->each(function ($post) use ($allUsers) {
            // Root comments
            $rootComments = Comment::factory(rand(2, 5))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
                'parent_id' => null,
            ]);

            // Replies to comments
            $rootComments->each(function ($comment) use ($post, $allUsers) {
                if (fake()->boolean(50)) {
                    Comment::factory(rand(1, 3))->create([
                        'post_id' => $post->id,
                        'user_id' => $allUsers->random()->id,
                        'parent_id' => $comment->id,
                    ]);
                }
            });
        });
    }
}