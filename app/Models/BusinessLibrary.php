<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLibrary extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'file_path', 'business_library_category_id'];

    public function category()
    {
        return $this->belongsTo(BusinessLibraryCategory::class, 'business_library_category_id');
    }
}
