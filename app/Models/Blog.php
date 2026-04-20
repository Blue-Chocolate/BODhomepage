<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'title', 'slug', 'image_url', 'published_at', 'is_published',
        'short_description', 'content', 'author', 'blog_category_id',
        'product_id', 'keyword', 'keyword_strength', 'search_intent',
        'category_name', 'tags', 'meta_description', 'summary',
        'content_html', 'word_count',
    ];

    protected $casts = [
        'published_at'     => 'datetime',
        'is_published'     => 'boolean',
        'keyword_strength' => 'float',
    ];

    public function getTagsArrayAttribute(): array
    {
        return $this->tags ? explode('|', $this->tags) : [];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}