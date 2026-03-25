<?php

// app/Models/FooterNavGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FooterNavGroup extends Model
{
    protected $fillable = ['title', 'title_en', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function links(): HasMany
    {
        return $this->hasMany(FooterNavLink::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    public static function getActive()
    {
        return static::where('is_active', true)
                     ->orderBy('sort_order')
                     ->with('links')
                     ->get();
    }
}
