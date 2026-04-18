<?php

namespace App\Models\Compliance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionRecommendation extends Model
{
    protected $table = 'submission_recommendations';

    protected $fillable = [
        'assessment_submission_id',
        'priority',
        'recommendation',
        'responsible_party',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssessmentSubmission::class, 'assessment_submission_id');
    }
}