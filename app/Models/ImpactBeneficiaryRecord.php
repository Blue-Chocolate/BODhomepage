<?php

namespace App\Models\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpactBeneficiaryRecord extends Model
{
    protected $table = 'impact_beneficiary_records';

    protected $fillable = [
        'impact_report_id',
        'period',
        'beneficiaries',
    ];

    protected $casts = [
        'beneficiaries' => 'integer',
    ];

    public function impactReport(): BelongsTo
    {
        return $this->belongsTo(ImpactReport::class);
    }
}