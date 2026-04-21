<?php

namespace App\Models\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramQuarter extends Model
{
    protected $table = 'program_quarters';

    protected $fillable = [
        'program_id',
        'quarter',
        'year',
        'budget',
        'actual_cost',
        'beneficiaries',
    ];

    protected $casts = [
        'budget'        => 'float',
        'actual_cost'   => 'float',
        'beneficiaries' => 'integer',
        'year'          => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    // ─── Computed ─────────────────────────────────────────────────────────────────

    /**
     * انحراف الميزانية %
     * ((ميزانية - تكلفة) / ميزانية) × 100
     */
    public function getBudgetVarianceAttribute(): ?float
    {
        if (! $this->budget || $this->budget == 0) {
            return null;
        }

        return round((($this->budget - $this->actual_cost) / $this->budget) * 100, 2);
    }
}