<?php

namespace App\Models\Organization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Organization extends Authenticatable   
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'liscense_number',
        'evaluation_date',
        'evaluation_duration',
        'evaluation_score',
        'evaluator_name',
        'evaluation_team',
        'representative_name',
        'approval_status',
        'approved_at',
    ];

    protected $casts = [
        'evaluation_date' => 'datetime',
        'approved_at'     => 'datetime',
        'evaluation_score' => 'float',
        'evaluation_duration' => 'integer',
        
    ];

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function approve(): void
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_at'     => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_at'     => null,
        ]);
    }
}