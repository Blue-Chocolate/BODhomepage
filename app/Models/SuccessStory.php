<?php 

// app/Models/SuccessStory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $fillable = [
        'title', 'title_en',
        'content', 'content_en',
        'image_path', 'video_url', 'video_embed',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function getActive()
    {
        return static::where('is_active', true)
                     ->orderBy('sort_order')
                     ->get();
    }
}