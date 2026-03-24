<?php 

// app/Models/NewsTag.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsTag extends Model
{
    protected $fillable = ['name', 'name_en', 'slug', 'wp_term_id'];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_tag', 'news_tag_id', 'news_id');
    }
}