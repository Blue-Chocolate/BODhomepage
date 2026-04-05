<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSolutionLink extends Model
{
    protected $table = 'digital_solution_links';

    protected $fillable = [
        'label',
        'label_en',
        'url',
        'open_in_new_tab',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public static function getActive()
    {
        return static::where('is_active', true)
                     ->orderBy('sort_order')
                     ->get();
    }
}