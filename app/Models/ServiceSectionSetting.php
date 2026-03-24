<?php
// app/Models/ServiceSectionSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSectionSetting extends Model
{
    protected $fillable = ['title', 'title_en', 'subtitle', 'subtitle_en'];

    public static function getInstance(): static
    {
        return static::firstOrCreate([]);
    }
}