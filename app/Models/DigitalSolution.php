<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSolution extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'image_path', 'digital_solution_type_id'];

    public function type()
    {
        return $this->belongsTo(DigitalSolutionType::class, 'digital_solution_type_id');
    }
}
