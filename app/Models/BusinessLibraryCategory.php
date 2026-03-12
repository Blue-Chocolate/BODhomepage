<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLibraryCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function resources()
    {
        return $this->hasMany(BusinessLibrary::class, 'business_library_category_id');
    }
}
