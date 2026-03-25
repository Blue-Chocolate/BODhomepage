<?php

// app/Models/FooterNavLink.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterNavLink extends Model
{
    protected $fillable = [
        'footer_nav_group_id', 'label', 'label_en',
        'url', 'open_in_new_tab', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FooterNavGroup::class, 'footer_nav_group_id');
    }
}