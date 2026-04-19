<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageHeroSection extends Model
{
    protected $table = 'homepage_herosection';

    protected $fillable = [
        'title',
        'description',
        'background_image',
        'background_video_url',
        'text',
        'subtext',
    ];

    protected $casts = [
        'background_image' => 'array',
    ];
}
