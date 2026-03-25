<?php

// app/Models/FooterSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'logo_image', 'tagline', 'tagline_en',
        'contact_email', 'contact_phone',
        'contact_address', 'contact_address_en', 'contact_map_url',
        'newsletter_enabled', 'newsletter_title', 'newsletter_title_en',
        'newsletter_placeholder', 'newsletter_placeholder_en',
        'newsletter_button_text', 'newsletter_button_text_en',
        'copyright_text', 'copyright_text_en',
        'back_to_top_enabled',
    ];

    protected $casts = [
        'newsletter_enabled'  => 'boolean',
        'back_to_top_enabled' => 'boolean',
    ];

    public static function getInstance(): static
    {
        return static::firstOrCreate([], [
            'tagline'                 => 'نبني المنظمات، نصنع الأثر',
            'newsletter_enabled'      => true,
            'newsletter_button_text'  => 'اشترك',
            'back_to_top_enabled'     => true,
            'copyright_text'          => 'جميع الحقوق محفوظة © ' . date('Y'),
        ]);
    }
}