<?php 

// app/Models/NewsCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsCategory extends Model
{
    protected $fillable = ['name', 'name_en', 'slug', 'wp_term_id'];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_category', 'news_category_id', 'news_id');
    }
}