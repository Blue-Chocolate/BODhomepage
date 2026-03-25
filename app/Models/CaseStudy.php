<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'title',
        'slug',
        'excerpt',
        'content_text',
        'status',
        'link',
        'image_url',
        'image_drive_file_id',
        'image_drive_link',
        'image_file_name',
        'image_upload_status',
        'author_id',
        'author_name',
        'category_id',
        'tags',
        'reading_time',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }
}