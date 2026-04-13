<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialInitiative extends Model
{
    protected $fillable = [
        'post_id',
        'title',
        'slug',
        'excerpt',
        'content_text',
        'image_url',
        'content_image_1',
        'image_drive_file_id',
        'image_drive_link',
        'image_file_name',
        'image_upload_status',
        'content_image_1_drive_file_id',
        'content_image_1_drive_link',
        'content_image_1_file_name',
        'content_image_1_upload_status',
        'category_id',
        'status',
        'link',
        'post_date',
        'post_modified',
    ];

    protected $casts = [
        'post_date'     => 'datetime',
        'post_modified' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }
}