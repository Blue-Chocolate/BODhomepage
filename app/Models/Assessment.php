<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use SoftDeletes;

    protected $table = 'assessments';

    protected $fillable = [
        'title',
        'description',
        'period_year',
        'opens_at',
        'closes_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opens_at'  => 'date',
        'closes_at' => 'date',
    ];

    public function axes(): HasMany
    {
        return $this->hasMany(AssessmentAxis::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssessmentSubmission::class);
    }

    /**
     * Total number of questions across all active axes.
     */
    public function getTotalQuestionsCountAttribute(): int
    {
        return $this->axes->sum(fn ($axis) => $axis->questions->count());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}