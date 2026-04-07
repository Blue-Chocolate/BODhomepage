<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'site',
        'title_ar',
        'title_en',
        'logo_url',
        'subtitle_ar',
        'subtitle_en',
        'background_image',
        'background_image_mobile',
        'background_video_url',
        'overlay_color',
        'overlay_opacity',
        'transition_effect',
        'display_duration',
        'cta_primary_text_ar',
        'cta_primary_text_en',
        'cta_primary_url',
        'cta_primary_style',
        'cta_secondary_text_ar',
        'cta_secondary_text_en',
        'cta_secondary_url',
        'cta_secondary_visible',
        'autoplay',
        'loop',
        'show_arrows',
        'show_dots',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'overlay_opacity'       => 'integer',
        'display_duration'      => 'integer',
        'sort_order'            => 'integer',
        'is_active'             => 'boolean',
        'autoplay'              => 'boolean',
        'loop'                  => 'boolean',
        'show_arrows'           => 'boolean',
        'show_dots'             => 'boolean',
        'cta_secondary_visible' => 'boolean',
    ];

    protected $appends = [
        'background_image_url',
        'background_image_mobile_url',
        'overlay_css',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Returns slides that belong to a specific site OR 'both'.
     */
    public function scopeForSite($query, string $site)
    {
        return $query->whereIn('site', [$site, 'both']);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image
            ? Storage::url($this->background_image)
            : null;
    }

    public function getBackgroundImageMobileUrlAttribute(): ?string
    {
        return $this->background_image_mobile
            ? Storage::url($this->background_image_mobile)
            : $this->background_image_url; // fallback to desktop
    }

    /**
     * Returns a CSS-ready rgba() string. e.g. "rgba(0,0,0,0.40)"
     */
    public function getOverlayCssAttribute(): string
    {
        [$r, $g, $b] = sscanf($this->overlay_color, '#%02x%02x%02x');
        $alpha = round($this->overlay_opacity / 100, 2);

        return "rgba({$r},{$g},{$b},{$alpha})";
    }
}