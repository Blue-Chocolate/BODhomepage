<?php

namespace App\Models\Governance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramGovernanceScore extends Model
{
    protected $table = 'program_governance_scores';

    protected $fillable = [
        'program_id',
        'year',
        'pillar_impact',
        'pillar_integrity',
        'pillar_empowerment',
        'pillar_innovation',
        'pillar_capacity',
        'pillar_sustainability',
        'overall_score',
        'total_budget',
        'total_beneficiaries',
        'budget_variance',
        'cost_per_beneficiary',
        'calculated_at',
    ];

    protected $casts = [
        'pillar_impact'         => 'float',
        'pillar_integrity'      => 'float',
        'pillar_empowerment'    => 'float',
        'pillar_innovation'     => 'float',
        'pillar_capacity'       => 'float',
        'pillar_sustainability' => 'float',
        'overall_score'         => 'float',
        'total_budget'          => 'float',
        'cost_per_beneficiary'  => 'float',
        'budget_variance'       => 'float',
        'calculated_at'         => 'datetime',
    ];

    // ─── Score Classification ─────────────────────────────────────────────────────

    public static array $levels = [
        ['min' => 80, 'max' => 100, 'label' => 'حوكمة متميزة',                'color' => '#10B981'],
        ['min' => 60, 'max' => 79,  'label' => 'حوكمة جيدة',                  'color' => '#3B82F6'],
        ['min' => 40, 'max' => 59,  'label' => 'حوكمة متوسطة',                'color' => '#F59E0B'],
        ['min' => 0,  'max' => 39,  'label' => 'حوكمة ضعيفة — تدخل عاجل',    'color' => '#EF4444'],
    ];

    public function getClassificationAttribute(): ?array
    {
        if ($this->overall_score === null) {
            return null;
        }

        foreach (static::$levels as $level) {
            if ($this->overall_score >= $level['min'] && $this->overall_score <= $level['max']) {
                return $level;
            }
        }

        return null;
    }

    public function getPillarsAttribute(): array
    {
        return [
            ['key' => 'pillar_impact',         'label' => 'الغاية والأثر',         'score' => $this->pillar_impact],
            ['key' => 'pillar_integrity',       'label' => 'النزاهة المالية',       'score' => $this->pillar_integrity],
            ['key' => 'pillar_empowerment',     'label' => 'التمكين المجتمعي',      'score' => $this->pillar_empowerment],
            ['key' => 'pillar_innovation',      'label' => 'الابتكار والتكيف',      'score' => $this->pillar_innovation],
            ['key' => 'pillar_capacity',        'label' => 'بناء القدرات',          'score' => $this->pillar_capacity],
            ['key' => 'pillar_sustainability',  'label' => 'الاستدامة المؤسسية',    'score' => $this->pillar_sustainability],
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}