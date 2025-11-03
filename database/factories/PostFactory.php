<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);
        $isPublished = fake()->boolean(70);
        
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'excerpt' => fake()->sentence(15),
            'body' => fake()->paragraphs(8, true),
            'status' => $isPublished ? 'published' : 'draft',
            'views' => fake()->numberBetween(0, 1000),
            'published_at' => $isPublished ? now()->subDays(rand(1, 30)) : null,
        ];
    }

    public function published()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function draft()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}