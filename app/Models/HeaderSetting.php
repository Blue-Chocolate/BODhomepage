<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    use HasFactory;

    protected $table = 'header_settings';

    protected $fillable = [
        'logo_image',
        'logo_text',
        'logo_url',
        'headline',
        'subheadline',
        'text',
        'background_image',
        'organizations_count',
        'experience_years',
        'projects_count',
    ];

    protected $casts = [
        'subheadline' => 'array',
        'text' => 'array',
    ];
}