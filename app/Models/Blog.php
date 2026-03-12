<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['title', 'slug','short_description', 'content', 'author', 'image_path', 'published_at', 'blog_category_id', 'is_published'];
    public function category()
    {
        return $this->belongsTo(BlogCateogry::class, 'blog_category_id');
    }
}
