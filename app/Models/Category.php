<?php

namespace App\Models;

// 1. IMPORT trait ini
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // 2. GUNAKAN trait ini di dalam class
    use HasFactory; 

    protected $fillable = ['name', 'slug', 'description'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    
    // Accessor untuk URL
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }
}