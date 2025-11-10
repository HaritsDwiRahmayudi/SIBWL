<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Pastikan ini ada

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.com'], // Kunci unik untuk mencari
            [
                'name' => 'Admin User',     
                'password' => Hash::make('password'), // password default adalah "password"
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
        
        // 2. Buat 10 User dummy (selain admin)
        $users = User::factory(10)->create();
        $allUsers = $users->push($admin); // Gabungkan admin dan user dummy

        // === INI BAGIAN YANG DIUBAH ===

        // 3. Buat Kategori Berita Indonesia yang spesifik
        $categories = collect([
            ['name' => 'Politik', 'slug' => 'politik'],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi'],
            ['name' => 'Olahraga', 'slug' => 'olahraga'],
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Gaya Hidup', 'slug' => 'gaya-hidup'],
            ['name' => 'Hiburan', 'slug' => 'hiburan'],
            ['name' => 'Internasional', 'slug' => 'internasional'],
            ['name' => 'Opini', 'slug' => 'opini'],
        ])->map(fn($cat) => Category::firstOrCreate(
            ['slug' => $cat['slug']], 
            ['name' => $cat['name'], 'description' => 'Berita seputar ' . $cat['name']]
        ));

        // 4. Buat Tag Berita Indonesia yang spesifik
        $tags = collect([
            ['name' => 'Pemilu 2024', 'slug' => 'pemilu-2024'],
            ['name' => 'Timnas Indonesia', 'slug' => 'timnas-indonesia'],
            ['name' => 'Start-up', 'slug' => 'start-up'],
            ['name' => 'Inflasi', 'slug' => 'inflasi'],
            ['name' => 'Bansos', 'slug' => 'bansos'],
            ['name' => 'AI', 'slug' => 'ai'],
            ['name' => 'Ibu Kota Nusantara', 'slug' => 'ikn'],
            ['name' => 'K-Pop', 'slug' => 'k-pop'],
            ['name' => 'Harga Emas', 'slug' => 'harga-emas'],
            ['name' => 'Info Cuaca', 'slug' => 'info-cuaca'],
        ])->map(fn($tag) => Tag::firstOrCreate(
            ['slug' => $tag['slug']], 
            ['name' => $tag['name']]
        ));

        // === SELESAI MENGUBAH ===

        // 5. Buat Postingan (Kode asli Anda, tidak perlu diubah)
        $categories->each(function ($category) use ($allUsers, $tags) {
            $posts = Post::factory(rand(5, 10))
                ->create([
                    'user_id' => $allUsers->random()->id,
                    'category_id' => $category->id,
                ]);

            // 6. Pasang Tag ke Postingan (Kode asli Anda)
            $posts->each(function ($post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(rand(1, 4))->pluck('id')->toArray()
                );
            });
        });

        // 7. Buat Komentar (Kode asli Anda)
        Post::published()->get()->each(function ($post) use ($allUsers) {
            // Komentar utama
            $rootComments = Comment::factory(rand(2, 5))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
                'parent_id' => null,
            ]);

            // Balasan Komentar
            $rootComments->each(function ($comment) use ($post, $allUsers) {
                if (fake()->boolean(50)) { // 50% kemungkinan ada balasan
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