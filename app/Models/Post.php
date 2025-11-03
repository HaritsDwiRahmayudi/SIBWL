<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 
        'body', 'cover_image', 'status', 'views', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Route model binding menggunakan slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('is_approved', true);
    }

    // Query Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('body', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->when($categorySlug, function($q) use ($categorySlug) {
            $q->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        });
    }

    public function scopeByTag($query, $tagSlug)
    {
        return $query->when($tagSlug, function($q) use ($tagSlug) {
            $q->whereHas('tags', function($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        });
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->orderByDesc('views')->limit($limit);
    }

    // Accessors & Mutators
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->body));
        return ceil($words / 200); // 200 words per minute
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->published_at?->format('F d, Y') ?? 'Not published';
    }

    // Helper methods
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at !== null && 
               $this->published_at->isPast();
    }
}