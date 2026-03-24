<?php 

// app/Models/News.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'wp_post_id', 'row_number',
        'title', 'excerpt', 'content_text', 'slug', 'reading_time',
        'status', 'published_at', 'modified_at',
        'author_id', 'author_name',
        'image_path', 'image_url', 'image_drive_file_id',
        'image_drive_link', 'image_file_name', 'image_upload_status',
        'link',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'modified_at'  => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category', 'news_id', 'news_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_tag', 'news_id', 'news_tag_id');
    }

    // Image URL helper
    public function getImageAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return $this->image_url;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'publish')
                     ->whereNotNull('published_at')
                     ->orderByDesc('published_at');
    }
}