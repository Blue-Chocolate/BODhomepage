<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    protected $fillable = [
        'logo_image', 'logo_text', 'logo_text_en', 'logo_url',
        'cta_text', 'cta_text_en', 'cta_url', 'cta_color',
        'cta_visible', 'cta_new_tab',
        'is_sticky', 'show_language_switcher',
    ];

    protected $casts = [
        'cta_visible'            => 'boolean',
        'cta_new_tab'            => 'boolean',
        'is_sticky'              => 'boolean',
        'show_language_switcher' => 'boolean',
    ];

    // Singleton — دايماً سطر واحد في الجدول
    public static function getInstance(): static
    {
        return static::firstOrCreate([], [
            'logo_text' => 'ولادة حلم',
            'cta_text'  => 'تواصل معنا',
            'cta_url'   => '/contact',
        ]);
    }
}