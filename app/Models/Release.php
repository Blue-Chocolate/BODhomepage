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
        'button_text',
        'title_guess',
        'card_text',
        'image_url',
    ];

    protected $casts = [
        'row_number'     => 'integer',
        'edition_number' => 'integer',
    ];

    /**
     * Scope to order by edition number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('edition_number');
    }
}