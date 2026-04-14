<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicPlan extends Model
{
    protected $fillable = [
        'post_id',
        'post_date',
        'post_modified',
        'status',
        'link',
        'title',
        'excerpt',
        'content_text',
        'image_url',
        'content_image_1',
        'content_image_2',
        'execution_report',
        'association_website',
        'image_drive_file_id',
        'image_drive_link',
        'image_file_name',
        'image_upload_status',
        'content_image_1_drive_file_id',
        'content_image_1_drive_link',
        'content_image_1_file_name',
        'content_image_1_upload_status',
        'content_image_2_drive_file_id',
        'content_image_2_drive_link',
        'content_image_2_file_name',
        'content_image_2_upload_status',
        'categories',
        'slug',
    ];

    protected $casts = [
        'post_date'     => 'datetime',
        'post_modified' => 'datetime',
        'post_id'       => 'integer',
        'categories'    => 'integer',
    ];
}