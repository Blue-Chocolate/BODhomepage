<?php 

// app/Models/Testimonial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'name_en',
        'organization', 'organization_en',
        'quote', 'quote_en',
        'rating', 'image_path',
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