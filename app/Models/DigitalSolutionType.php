<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSolutionType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function digitalSolutions()
    {
        return $this->hasMany(DigitalSolution::class, 'digital_solution_type_id');
    }
}
