<?php

namespace App\Models\Governance;

use App\Models\Organization\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    use SoftDeletes;

    protected $table = 'programs';

    protected $fillable = [
        'organization_id',
        'name',
        'status',
        'total_actual_cost',
        'execution_duration',
        'resource_efficiency',
        'cost_per_beneficiary',
        'deleted_at',
        'created_at',
        'updated_at',
        'is_active',
    ];

    protected $casts = [
        'total_actual_cost'    => 'float',
        'resource_efficiency'  => 'float',
        'cost_per_beneficiary' => 'float',
        'is_active'            => 'boolean',
    ];

    // ─── Status Score Map (for pillar_innovation) ────────────────────────────────
    public static array $statusScores = [
        'completed'   => 95,
        'in_progress' => 70,
        'planning'    => 45,
        'suspended'   => 20,
    ];

    public static array $statusLabels = [
        'completed'   => 'مكتمل',
        'in_progress' => 'جار التنفيذ',
        'planning'    => 'في مرحلة التخطيط',
        'suspended'   => 'متوقف',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function quarters(): HasMany
    {
        return $this->hasMany(ProgramQuarter::class)->orderBy('year')->orderBy('quarter');
    }

    public function governanceScore(): HasOne
    {
        return $this->hasOne(ProgramGovernanceScore::class);
    }

    public function impactReport(): HasOne
    {
        return $this->hasOne(ImpactReport::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusScoreAttribute(): int
    {
        return static::$statusScores[$this->status] ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}