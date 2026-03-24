<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavItem extends Model
{
    protected $fillable = [
        'label', 'label_en', 'url',
        'open_in_new_tab', 'is_active',
        'sort_order', 'parent_id',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public static function getMenu(): \Illuminate\Database\Eloquent\Collection
    {
        return cache()->remember('nav_menu', 300, fn() =>
            static::whereNull('parent_id')
                  ->where('is_active', true)
                  ->orderBy('sort_order')
                  ->with('children')
                  ->get()
        );
    }
}