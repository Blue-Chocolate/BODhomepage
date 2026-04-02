<?php

namespace App\Modules\AnnualPlans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnualPlan extends Model
{
    use SoftDeletes;

    protected $table = 'annual_plans';

    protected $fillable = [
        'post_id',
        'title',
        'slug',
        'excerpt',
        'content_text',
        'link',
        'status',
        'category_id',

        'image_url',
        'image_file_name',
        'image_drive_file_id',
        'image_drive_link',
        'image_upload_status',

        'content_image_1_url',
        'content_image_1_file_name',
        'content_image_1_drive_file_id',
        'content_image_1_drive_link',
        'content_image_1_upload_status',

        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}