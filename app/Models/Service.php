<?php 

// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'title_en',
        'description', 'description_en',
        'icon', 'image_path',
        'cta_text', 'cta_text_en', 'cta_url',
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