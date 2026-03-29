<?php 

// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'title_en',
        'description', 'description_en',
        'sort_order', 'is_active',
        // keep icon/image/cta if still needed, remove if not
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subServices()
    {
        return $this->hasMany(SubService::class)->orderBy('sort_order');
    }

    public function activeSubServices()
    {
        return $this->subServices()->where('is_active', true);
    }

    public static function getActive()
    {
        return static::with('activeSubServices')
                     ->where('is_active', true)
                     ->orderBy('sort_order')
                     ->get();
    }
}