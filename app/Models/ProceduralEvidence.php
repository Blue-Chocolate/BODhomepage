<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ProceduralEvidence extends Model
{
    use HasFactory;
    protected $table = 'procedural_evidences';
    protected $fillable = [
        'row_number',
        'post_id',
        'date',
        'modified',
        'status',
        'link',
        'title',
        'excerpt',
        'content_text',
        'image_url',
        'image_drive_file_id',
        'image_drive_link',
        'image_file_name',
        'image_upload_status',
        'categories',
        'slug',
    ];

    protected $casts = [
        'date' => 'datetime',
        'modified' => 'datetime',
    ];
}