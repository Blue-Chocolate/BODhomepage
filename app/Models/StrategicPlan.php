<?php
// app/Models/StrategicPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StrategicPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'title',
        'slug',
        'excerpt',
        'content_text',
        'status',
        'category_id',
        'image_url',
        'content_image_1',
        'content_image_2',
        'image_drive_file_id',
        'content_image_1_drive_file_id',
        'content_image_2_drive_file_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function isPublished(): bool
    {
        return $this->status === 'publish';
    }

    // Scope for API/frontend
    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }
}