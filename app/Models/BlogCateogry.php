<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCateogry extends Model
{
    protected $table = 'blog_categories';
    protected $fillable = ['name', 'slug', 'description'];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}
