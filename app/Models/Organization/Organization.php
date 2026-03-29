<?php

namespace App\Models\Organization;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name', 'email', 'phone',
        'liscense_number', 'approval_status', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];
}
