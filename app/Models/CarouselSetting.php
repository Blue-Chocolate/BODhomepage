<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselSetting extends Model
{
    protected $fillable = [
        'section', 'auto_play', 'auto_play_speed',
        'slides_to_show', 'show_dots', 'show_arrows',
    ];

    protected $casts = [
        'auto_play'   => 'boolean',
        'show_dots'   => 'boolean',
        'show_arrows' => 'boolean',
    ];

    public static function forSection(string $section): static
    {
        return static::firstOrCreate(['section' => $section], [
            'auto_play'       => true,
            'auto_play_speed' => 3000,
            'slides_to_show'  => 3,
            'show_dots'       => true,
            'show_arrows'     => true,
        ]);
    }
}   