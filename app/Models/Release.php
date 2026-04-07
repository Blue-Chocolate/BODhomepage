<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_number',
        'edition_number',
        'file_url',
        'direct_download_url',
        'cover_image_url',
        'image_drive_link',
        'image_file_name',
        'image_drive_file_id',
        'button_text',
        'title_guess',
        'excerpt',
        'card_text',
        'image_upload_status',
        'image_url',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'edition_number' => 'integer',
    ];

    // Add to Release model
public function getImageUrlAttribute($value)
{
    // If image_url exists, use it
    if ($value) {
        return $value;
    }
    
    // Otherwise, try to generate from drive link
    if ($this->image_drive_file_id) {
        return "https://drive.google.com/uc?export=view&id={$this->image_drive_file_id}";
    }
    
    return null;
}
}