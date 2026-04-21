<?php

namespace App\Models\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImpactReport extends Model
{
    use SoftDeletes;

    protected $table = 'impact_reports';

    protected $fillable = [
        'program_id',
        'year',
        'social_inclusion',
        'service_quality',
        'advocacy',
        'investment_value',
        'social_impact_value',
        'overall_impact_score',
        'sroi',
        'impact_classification',
        'calculated_at',
    ];

    protected $casts = [
        'social_inclusion'     => 'float',
        'service_quality'      => 'float',
        'advocacy'             => 'float',
        'investment_value'     => 'float',
        'social_impact_value'  => 'float',
        'overall_impact_score' => 'float',
        'sroi'                 => 'float',
        'calculated_at'        => 'datetime',
        'year'                 => 'integer',
    ];

    // ─── Classification ───────────────────────────────────────────────────────────

    public static array $classifications = [
        ['min' => 75, 'label' => 'أثر ممتاز',       'color' => '#10B981', 'icon' => '✅'],
        ['min' => 55, 'label' => 'أثر جيد',          'color' => '#3B82F6', 'icon' => '✅'],
        ['min' => 0,  'label' => 'يحتاج تحسين',      'color' => '#EF4444', 'icon' => '❌'],
    ];

    public function getClassificationDataAttribute(): ?array
    {
        if ($this->overall_impact_score === null) {
            return null;
        }

        foreach (static::$classifications as $c) {
            if ($this->overall_impact_score >= $c['min']) {
                return $c;
            }
        }

        return null;
    }

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function beneficiaryRecords(): HasMany
    {
        return $this->hasMany(ImpactBeneficiaryRecord::class)->orderBy('period');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function getTotalBeneficiariesAttribute(): int
    {
        return $this->beneficiaryRecords->sum('beneficiaries');
    }
}