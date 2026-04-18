<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    protected $table = 'assessment_questions';

    protected $fillable = [
        'assessment_axis_id',
        'title',
        'description',
        'guidance',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function axis(): BelongsTo
    {
        return $this->belongsTo(AssessmentAxis::class, 'assessment_axis_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SubmissionAnswer::class);
    }
}