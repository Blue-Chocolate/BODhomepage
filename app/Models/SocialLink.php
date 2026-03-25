<?php

// app/Models/SocialLink.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'platform', 'label', 'url', 'icon',
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