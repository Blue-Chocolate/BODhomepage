<?php 

// app/Models/WhoAreWe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhoAreWe extends Model
{
    protected $table = 'who_are_we';

    protected $fillable = [
        'title', 'title_en',
        'description', 'description_en',
        'image_path',
        'vision', 'vision_en',
        'mission', 'mission_en',
    ];

    public function coreValues(): HasMany
    {
        return $this->hasMany(CoreValue::class);
    }

    public static function getInstance(): static
    {
        return static::firstOrCreate([]);
    }
}