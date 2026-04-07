<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_number',
         'edition_number',
        'post_id',
        'date',
        'modified',
        'status',
        'link',
        'title',
        'excerpt',
        'content_text',
        'author_id',
        'author_name',
        'image_url',
        'image_drive_file_id',
        'image_drive_link',
        'image_file_name',
        'image_upload_status',
        'categories',
        'tags',
        'slug',
        'reading_time',
    ];

    protected $casts = [
        'row_number' => 'integer',
          'edition_number' => 'integer',
        'post_id'    => 'integer',
        'author_id'  => 'integer',
        'categories' => 'integer',
        'date'       => 'datetime',
        'modified'   => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }
}